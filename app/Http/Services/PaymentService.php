<?php

namespace App\Http\Services;

use App\Models\Order;
use App\Models\Wallet;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct()
    {
        // Set Stripe API key
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Process payment based on payment method
     */
    public function processPayment(Order $order, array $paymentData = []): array
    {
        return match($order->payment_method) {
            'wallet' => $this->processWalletPayment($order),
            'card' => $this->processStripePayment($order, $paymentData),
            'cash' => $this->processCashPayment($order),
            default => throw new \Exception('Invalid payment method'),
        };
    }

    /**
     * Process wallet payment
     */
    protected function processWalletPayment(Order $order): array
    {
        return DB::transaction(function () use ($order) {
            $user = $order->user;
            $wallet = $user->wallet;

            if (!$wallet) {
                throw new \Exception('User does not have a wallet');
            }

            if (!$wallet->hasSufficientBalance($order->total)) {
                throw new \Exception('Insufficient wallet balance. Current balance: ' . $wallet->balance . ' SAR');
            }

            // Process payment
            $transaction = $wallet->processPayment(
                $order->total,
                $order->id,
                "Payment for order #{$order->order_number}"
            );

            // Update order payment status
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'Payment successful via wallet',
                'transaction_id' => $transaction->id,
                'remaining_balance' => $wallet->fresh()->balance,
            ];
        });
    }

    /**
     * Process Stripe payment (Card)
     */
    protected function processStripePayment(Order $order, array $paymentData): array
    {
        try {
            // Create or retrieve payment intent
            if (isset($paymentData['payment_intent_id'])) {
                // Confirm existing payment intent
                $paymentIntent = PaymentIntent::retrieve($paymentData['payment_intent_id']);

                if ($paymentIntent->status === 'requires_confirmation') {
                    $paymentIntent = $paymentIntent->confirm();
                }
            } else {
                // Create new payment intent
                $paymentIntent = PaymentIntent::create([
                    'amount' => $order->total * 100, // Convert to cents
                    'currency' => 'sar',
                    'description' => "Payment for order #{$order->order_number}",
                    'metadata' => [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'user_id' => $order->user_id,
                    ],
                    'automatic_payment_methods' => [
                        'enabled' => true,
                    ],
                ]);
            }

            // Update order with payment intent
            $order->update([
                'stripe_payment_intent_id' => $paymentIntent->id,
            ]);

            // Check payment status
            if ($paymentIntent->status === 'succeeded') {
                $order->update([
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                    'status' => 'confirmed',
                    'confirmed_at' => now(),
                ]);

                return [
                    'success' => true,
                    'message' => 'Payment successful',
                    'payment_intent_id' => $paymentIntent->id,
                ];
            } elseif ($paymentIntent->status === 'requires_payment_method') {
                return [
                    'success' => false,
                    'requires_payment_method' => true,
                    'message' => 'Payment requires payment method',
                    'client_secret' => $paymentIntent->client_secret,
                    'payment_intent_id' => $paymentIntent->id,
                ];
            } elseif ($paymentIntent->status === 'requires_action') {
                return [
                    'success' => false,
                    'requires_action' => true,
                    'message' => 'Payment requires additional action',
                    'client_secret' => $paymentIntent->client_secret,
                    'payment_intent_id' => $paymentIntent->id,
                ];
            } else {
                throw new \Exception('Payment failed: ' . $paymentIntent->status);
            }
        } catch (\Stripe\Exception\CardException $e) {
            $order->update(['payment_status' => 'failed']);
            throw new \Exception('Card payment failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            $order->update(['payment_status' => 'failed']);
            throw new \Exception('Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Process cash payment (COD - Cash on Delivery)
     */
    protected function processCashPayment(Order $order): array
    {
        // For COD, order stays pending until delivery
        $order->update([
            'payment_status' => 'pending',
            // Status remains 'pending' until admin confirms
        ]);

        return [
            'success' => true,
            'message' => 'Order placed successfully. Payment will be collected on delivery.',
        ];
    }

    /**
     * Confirm Stripe payment webhook
     */
    public function handleStripeWebhook(array $payload): void
    {
        $paymentIntent = $payload['data']['object'];

        $order = Order::where('stripe_payment_intent_id', $paymentIntent['id'])->first();

        if (!$order) {
            return;
        }

        if ($paymentIntent['status'] === 'succeeded') {
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);
        } elseif ($paymentIntent['status'] === 'payment_failed') {
            $order->update([
                'payment_status' => 'failed',
            ]);
        }
    }

    /**
     * Process refund
     */
    public function processRefund(Order $order): array
    {
        return DB::transaction(function () use ($order) {
            if ($order->payment_status !== 'paid') {
                throw new \Exception('Order is not paid, cannot refund');
            }

            if ($order->payment_method === 'wallet') {
                // Refund to wallet
                $wallet = $order->user->wallet;
                $wallet->refund($order->total, $order->id, "Refund for order #{$order->order_number}");

                $order->update([
                    'payment_status' => 'refunded',
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                ]);

                return [
                    'success' => true,
                    'message' => 'Refund processed to wallet',
                ];
            } elseif ($order->payment_method === 'card' && $order->stripe_payment_intent_id) {
                // Refund via Stripe
                $refund = \Stripe\Refund::create([
                    'payment_intent' => $order->stripe_payment_intent_id,
                ]);

                $order->update([
                    'payment_status' => 'refunded',
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                ]);

                return [
                    'success' => true,
                    'message' => 'Refund processed via Stripe',
                    'refund_id' => $refund->id,
                ];
            } else {
                // Cash orders don't need refund processing
                $order->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                ]);

                return [
                    'success' => true,
                    'message' => 'Order cancelled (no refund needed for cash payment)',
                ];
            }
        });
    }

    /**
     * Get payment intent client secret (for frontend)
     */
    public function createPaymentIntent(Order $order): array
    {
        $paymentIntent = PaymentIntent::create([
            'amount' => $order->total * 100,
            'currency' => 'sar',
            'description' => "Payment for order #{$order->order_number}",
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ],
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);

        $order->update([
            'stripe_payment_intent_id' => $paymentIntent->id,
        ]);

        return [
            'client_secret' => $paymentIntent->client_secret,
            'payment_intent_id' => $paymentIntent->id,
        ];
    }
}
