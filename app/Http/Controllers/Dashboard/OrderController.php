<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Services\OrderService;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display listing of all orders (Admin)
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'status',
            'payment_method',
            'user_id',
            'start_date',
            'end_date',
            'sort_by',
            'sort_direction',
            'per_page'
        ]);

        $orders = $this->orderService->getAllOrders($filters);

        // Get users for filter dropdown
        $users = User::select('id', 'name')->orderBy('name')->get();

        // Status options
        $statuses = [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        // Payment methods
        $paymentMethods = [
            'cash' => 'Cash',
            'wallet' => 'Wallet',
            'card' => 'Card',
        ];

        return view('dashboard.orders.index', compact(
            'orders',
            'users',
            'statuses',
            'paymentMethods'
        ));
    }

    /**
     * Show order details
     */
    public function show(Order $order)
    {
        $order = $this->orderService->getOrderDetails($order);

        return view('dashboard.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        try {
            $this->orderService->updateOrderStatus(
                $order,
                $request->validated()['status']
            );

            return redirect()
                ->route('dashboard.orders.show', $order)
                ->with('success', 'Order status updated successfully!');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to update status: ' . $e->getMessage()]);
        }
    }

    /**
     * Cancel order
     */
    public function cancel(Order $order)
    {
        try {
            $this->orderService->cancelOrder($order);

            return redirect()
                ->route('dashboard.orders.show', $order)
                ->with('success', 'Order cancelled successfully!');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to cancel order: ' . $e->getMessage()]);
        }
    }
}
