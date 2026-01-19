<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Models\Notification;

class SendOrderStatusNotification
{
    /**
     * Handle the event.
     */
    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order;
        $newStatus = $event->newStatus;

        $messages = [
            'confirmed' => "Your order #{$order->order_number} has been confirmed!",
            'processing' => "Your order #{$order->order_number} is now being processed.",
            'completed' => "Your order #{$order->order_number} has been completed. Thank you!",
            'cancelled' => "Your order #{$order->order_number} has been cancelled.",
        ];

        Notification::create([
            'user_id' => $order->user_id,
            'title' => 'Order Status Updated',
            'message' => $messages[$newStatus] ?? "Order status changed to {$newStatus}",
            'type' => 'order',
            'is_read' => false,
        ]);

        // TODO: Send email notification
        // TODO: Send SMS notification
    }
}
