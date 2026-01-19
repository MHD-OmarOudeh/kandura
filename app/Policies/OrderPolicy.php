<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine if user can view any orders (Admin only)
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage orders');
    }

    /**
     * Determine if user can view the order
     */
    public function view(User $user, Order $order): bool
    {
        // User can view their own orders
        if ($user->id === $order->user_id) {
            return true;
        }

        // Admin can view all orders
        return $user->hasPermissionTo('manage orders');
    }

    /**
     * Determine if user can create orders
     */
    public function create(User $user): bool
    {
        // All authenticated users can create orders
        return true;
    }

    /**
     * Determine if user can update order status (Admin only)
     */
    public function updateStatus(User $user, Order $order): bool
    {
        return $user->hasPermissionTo('manage orders');
    }

    /**
     * Determine if user can cancel the order
     */
    public function cancel(User $user, Order $order): bool
    {
        // User can cancel their own pending/confirmed orders
        if ($user->id === $order->user_id && $order->canBeCancelled()) {
            return true;
        }

        // Admin can cancel any order
        return $user->hasPermissionTo('manage orders');
    }

    /**
     * Determine if user can delete the order
     */
    public function delete(User $user, Order $order): bool
    {
        // Only admin can permanently delete
        return $user->hasPermissionTo('manage orders');
    }
    /**
     * Determine if user can pay for the order
     */
    public function pay(User $user, Order $order): bool
    {
        // User can pay for their own unpaid orders
        return $user->id === $order->user_id && $order->payment_status !== 'paid';
    }

    /**
     * Determine if user can refund the order
     */
    public function refund(User $user, Order $order): bool
    {
        // Only admin can process refunds
        return $user->hasPermissionTo('manage orders');
    }
}
