<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Models\Notification;

class SendOrderCreatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;

        // Create notification for user
        Notification::create([
            'user_id' => $order->user_id,
            'title' => 'Order Created',
            'message' => "Your order #{$order->order_number} has been created successfully. Total: {$order->total} SAR",
            'type' => 'order',
            'is_read' => false,
        ]);

        // TODO: Send email notification
        // TODO: Send SMS notification
    }
}
