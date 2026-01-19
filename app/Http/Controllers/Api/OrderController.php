<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Http\Services\OrderService;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Get user's orders
     * GET /api/orders
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $filters = $request->only([
                'status',
                'payment_method',
                'start_date',
                'end_date',
                'search',
                'sort_by',
                'sort_direction',
                'per_page'
            ]);

            $orders = $this->orderService->getUserOrders($user, $filters);

            return $this->success(
                OrderResource::collection($orders)->response()->getData(true),
                'Orders retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve orders', $e->getMessage(), 500);
        }
    }

    /**
     * Get all orders (Admin)
     * GET /api/orders/all
     */
    public function all(Request $request)
    {
        try {
            $this->authorize('viewAny', Order::class);

            $filters = $request->only([
                'user_id',
                'status',
                'payment_method',
                'start_date',
                'end_date',
                'search',
                'sort_by',
                'sort_direction',
                'per_page'
            ]);

            $orders = $this->orderService->getAllOrders($filters);

            return $this->success(
                OrderResource::collection($orders)->response()->getData(true),
                'All orders retrieved successfully'
            );
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbidden('Unauthorized access');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve orders', $e->getMessage(), 500);
        }
    }

    /**
     * Create new order
     * POST /api/orders
     */
    public function store(StoreOrderRequest $request)
    {
        try {
            $user = $request->user();
            $order = $this->orderService->createOrder($user, $request->validated());

            return $this->success(
                new OrderResource($order),
                'Order created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->error('Failed to create order', $e->getMessage(), 500);
        }
    }

    /**
     * Get specific order
     * GET /api/orders/{order}
     */
    public function show(Request $request, Order $order)
    {
        try {
            $this->authorize('view', $order);

            $orderDetails = $this->orderService->getOrderDetails($order);

            return $this->success(
                new OrderResource($orderDetails),
                'Order retrieved successfully'
            );
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbidden('You do not have permission to view this order');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve order', $e->getMessage(), 500);
        }
    }

    /**
     * Update order status (Admin)
     * PUT /api/orders/{order}/status
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        try {
            $this->authorize('updateStatus', $order);

            $updatedOrder = $this->orderService->updateOrderStatus(
                $order,
                $request->validated()['status']
            );

            return $this->success(
                new OrderResource($updatedOrder->load(['orderItems.design', 'address', 'user'])),
                'Order status updated successfully'
            );
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbidden('Unauthorized access');
        } catch (\Exception $e) {
            return $this->error('Failed to update order status', $e->getMessage(), 500);
        }
    }

    /**
     * Cancel order
     * DELETE /api/orders/{order}
     */
    public function destroy(Request $request, Order $order)
    {
        try {
            $this->authorize('cancel', $order);

            $this->orderService->cancelOrder($order);

            return $this->success(null, 'Order cancelled successfully');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbidden('You do not have permission to cancel this order');
        } catch (\Exception $e) {
            return $this->error('Failed to cancel order', $e->getMessage(), 500);
        }
    }
}
