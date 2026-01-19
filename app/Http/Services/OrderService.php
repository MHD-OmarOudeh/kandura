<?php

namespace App\Http\Services;

use App\Models\Order;
use App\Models\Design;
use App\Models\Coupon;
use App\Models\User;
use App\Events\OrderCreated;
use App\Events\OrderStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Services\PaymentService;


class OrderService
{

protected PaymentService $paymentService;

public function __construct(PaymentService $paymentService)
{
    $this->paymentService = $paymentService;
}

/**
 * Create new order with payment processing
 */
    public function createOrder(User $user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            // Validate items exist
            $this->validateOrderItems($data['items']);

            // Calculate totals (with coupon if provided)
            $calculations = $this->calculateOrderTotals($user, $data['items'], $data['coupon_code'] ?? null);

            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user->id,
                'address_id' => $data['address_id'],
                'subtotal' => $calculations['subtotal'],
                'tax' => $calculations['tax'],
                'shipping_cost' => $calculations['shipping_cost'],
                'discount' => $calculations['discount'],
                'total' => $calculations['total'],
                'payment_method' => $data['payment_method'] ?? 'cash',
                'payment_status' => 'pending',
                'status' => 'pending',
                'coupon_id' => $calculations['coupon_id'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Attach items
            $this->attachOrderItems($order, $data['items']);

            // Mark coupon as used
            if ($calculations['coupon_id']) {
                $coupon = Coupon::find($calculations['coupon_id']);
                $coupon->markAsUsed($user, $order);
            }

            // Process payment if wallet
            if ($order->payment_method === 'wallet') {
                try {
                    $this->paymentService->processPayment($order);
                } catch (\Exception $e) {
                    throw new \Exception('Wallet payment failed: ' . $e->getMessage());
                }
            }

            // Fire event
            event(new OrderCreated($order));

            return $order->fresh()->load(['orderItems.design.images', 'address.city', 'coupon']);
        });
    }
    /**
     * Get user orders with filters and pagination
     */
    public function getUserOrders(User $user, array $filters = [])
    {
        $query = Order::with(['address.city', 'orderItems.design.images', 'coupon'])
            ->forUser($user->id);

        // Apply filters
        $query = $this->applyFilters($query, $filters);

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get all orders (Admin)
     */
    public function getAllOrders(array $filters = [])
    {
        $query = Order::with(['user', 'address.city', 'orderItems.design', 'coupon']);

        // Apply filters
        $query = $this->applyFilters($query, $filters);

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters($query, array $filters)
    {
        // Search
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        // Payment method filter
        if (!empty($filters['payment_method'])) {
            $query->byPaymentMethod($filters['payment_method']);
        }

        // Date range
        if (!empty($filters['start_date']) || !empty($filters['end_date'])) {
            $query->betweenDates(
                $filters['start_date'] ?? null,
                $filters['end_date'] ?? null
            );
        }

        // User filter (for admin)
        if (!empty($filters['user_id'])) {
            $query->forUser($filters['user_id']);
        }

        return $query;
    }

    /**
     * Create new order
     */


    /**
     * Validate order items
     */
    protected function validateOrderItems(array $items)
    {
        foreach ($items as $item) {
            $design = Design::find($item['design_id']);

            if (!$design) {
                throw new \Exception("Design with ID {$item['design_id']} not found");
            }

            if ($item['quantity'] < 1) {
                throw new \Exception("Quantity must be at least 1");
            }
        }
    }

    /**
     * Calculate order totals
     */
    protected function calculateOrderTotals(User $user, array $items, ?string $couponCode = null)
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $design = Design::findOrFail($item['design_id']);
            $subtotal += $design->price * $item['quantity'];
        }

        // Tax (15% - Saudi VAT)
        $tax = $subtotal * 0.15;

        // Shipping cost
        $shippingCost = $this->calculateShippingCost($subtotal);

        // Discount from coupon
        $discount = 0;
        $couponId = null;

        if ($couponCode) {
            $coupon = Coupon::where('code', strtoupper($couponCode))
                ->where('is_active', true)
                ->first();

            if ($coupon) {
                // Validate coupon
                $validation = $coupon->validate($user, $subtotal);
                if ($validation['valid']) {
                    $discount = $coupon->calculateDiscount($subtotal);
                    $couponId = $coupon->id;
                } else {
                    throw new \Exception($validation['message']);
                }
            } else {
                throw new \Exception('Invalid or inactive coupon code');
            }
        }

        // Total = Subtotal + Tax + Shipping - Discount
        $total = $subtotal + $tax + $shippingCost - $discount;

        return [
            'subtotal' => round($subtotal, 2),
            'tax' => round($tax, 2),
            'shipping_cost' => round($shippingCost, 2),
            'discount' => round($discount, 2),
            'total' => round($total, 2),
            'coupon_id' => $couponId,
        ];
    }

    /**
     * Calculate shipping cost
     */
    protected function calculateShippingCost(float $subtotal): float
    {
        // Free shipping for orders over 500 SAR
        if ($subtotal >= 500) {
            return 0;
        }

        return 50; // Fixed shipping cost
    }

    /**
     * Attach items to order
     */
    protected function attachOrderItems(Order $order, array $items): void
    {
        foreach ($items as $item) {
            $design = Design::with(['measurements', 'designOptions', 'images'])
                ->findOrFail($item['design_id']);

            $unitPrice = $design->price;
            $totalPrice = $unitPrice * $item['quantity'];

            // Create snapshot of design
            $snapshot = [
                'name' => $design->name,
                'price' => $design->price,
                'fabric_type' => $design->fabric_type,
                'color' => $design->color,
                'embroidery' => $design->embroidery,
                'measurements' => $design->measurements->toArray(),
                'design_options' => $design->designOptions->toArray(),
                'images' => $design->images->pluck('image_path')->toArray(),
            ];

            $order->orderItems()->create([
                'design_id' => $design->id,
                'quantity' => $item['quantity'],
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'design_snapshot' => $snapshot,
            ]);
        }
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Order $order, string $newStatus): Order
    {
        $oldStatus = $order->status;

        $order->update([
            'status' => $newStatus,
            $this->getStatusTimestampField($newStatus) => now(),
        ]);

        // Fire event
        event(new OrderStatusChanged($order, $oldStatus, $newStatus));

        return $order->fresh();
    }

    /**
     * Get status timestamp field name
     */
    protected function getStatusTimestampField(string $status): string
    {
        return match($status) {
            'confirmed' => 'confirmed_at',
            'processing' => 'processing_at',
            'completed' => 'completed_at',
            'cancelled' => 'cancelled_at',
            default => 'updated_at',
        };
    }

    /**
     * Cancel order
     */
    public function cancelOrder(Order $order): Order
    {
        if (!$order->canBeCancelled()) {
            throw new \Exception('This order cannot be cancelled');
        }

        return $this->updateOrderStatus($order, 'cancelled');
    }

    /**
     * Get order details
     */
    public function getOrderDetails(Order $order): Order
    {
        return $order->load([
            'user',
            'address.city',
            'orderItems.design.images',
            'orderItems.design.measurements',
            'orderItems.design.designOptions',
            'coupon',
            'invoice',
        ]);
    }
}
