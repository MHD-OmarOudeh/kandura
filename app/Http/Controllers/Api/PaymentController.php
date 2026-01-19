<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPaymentRequest;
use App\Http\Services\PaymentService;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Process payment for an order
     * POST /api/orders/{order}/payment
     */
    public function processPayment(ProcessPaymentRequest $request, Order $order)
    {
        try {
            $this->authorize('pay', $order);

            // Check if already paid
            if ($order->payment_status === 'paid') {
                return $this->error('Order is already paid', null, 400);
            }

            $result = $this->paymentService->processPayment($order, $request->validated());

            return $this->success($result, $result['message']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbidden('You do not have permission to pay for this order');
        } catch (\Exception $e) {
            return $this->error('Payment failed', $e->getMessage(), 500);
        }
    }

    /**
     * Create payment intent (for Stripe)
     * POST /api/orders/{order}/payment-intent
     */
    public function createPaymentIntent(Request $request, Order $order)
    {
        try {
            $this->authorize('pay', $order);

            if ($order->payment_method !== 'card') {
                return $this->error('Payment intent is only for card payments', null, 400);
            }

            if ($order->payment_status === 'paid') {
                return $this->error('Order is already paid', null, 400);
            }

            $result = $this->paymentService->createPaymentIntent($order);

            return $this->success($result, 'Payment intent created successfully');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbidden('Unauthorized access');
        } catch (\Exception $e) {
            return $this->error('Failed to create payment intent', $e->getMessage(), 500);
        }
    }

    /**
     * Webhook handler for Stripe
     * POST /api/webhooks/stripe
     */
    public function stripeWebhook(Request $request)
    {
        try {
            $payload = $request->all();

            // Verify webhook signature
            $webhookSecret = config('services.stripe.webhook_secret');
            $signature = $request->header('Stripe-Signature');

            \Stripe\Webhook::constructEvent(
                $request->getContent(),
                $signature,
                $webhookSecret
            );

            // Handle the event
            if ($payload['type'] === 'payment_intent.succeeded' ||
                $payload['type'] === 'payment_intent.payment_failed') {
                $this->paymentService->handleStripeWebhook($payload);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Process refund
     * POST /api/orders/{order}/refund
     */
    public function refund(Request $request, Order $order)
    {
        try {
            $this->authorize('refund', $order);

            $result = $this->paymentService->processRefund($order);

            return $this->success($result, $result['message']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbidden('Unauthorized access');
        } catch (\Exception $e) {
            return $this->error('Refund failed', $e->getMessage(), 500);
        }
    }
}
