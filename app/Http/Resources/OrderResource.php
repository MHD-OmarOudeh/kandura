<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,

            // User info (only for admin)
            'user' => $this->when(
                $request->user() && $request->user()->hasPermissionTo('manage orders'),
                [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'phone' => $this->user->phone,
                ]
            ),

            // Address
            'address' => new AddressResource($this->whenLoaded('address')),

            // Pricing
            'pricing' => [
                'subtotal' => (float) $this->subtotal,
                'tax' => (float) $this->tax,
                'shipping_cost' => (float) $this->shipping_cost,
                'discount' => (float) $this->discount,
                'total' => (float) $this->total,
            ],

            // Payment
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'payment_status_label' => $this->getPaymentStatusLabel(),
            'paid_at' => $this->paid_at?->toISOString(),

            // Order Status
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),

            // Coupon (only if exists and loaded)
            'coupon' => $this->when($this->relationLoaded('coupon') && $this->coupon, [
                'code' => $this->coupon?->code,
                'discount_percentage' => $this->coupon?->discount_percentage,
            ]),

            // Items
            'items' => OrderItemResource::collection($this->whenLoaded('orderItems')),
            'total_items' => $this->when(
                $this->relationLoaded('orderItems'),
                fn() => $this->orderItems->sum('quantity'),
                0
            ),

            // Notes
            'notes' => $this->notes,

            // Timestamps
            'timestamps' => [
                'created_at' => $this->created_at?->toISOString(),
                'confirmed_at' => $this->confirmed_at?->toISOString(),
                'processing_at' => $this->processing_at?->toISOString(),
                'completed_at' => $this->completed_at?->toISOString(),
                'cancelled_at' => $this->cancelled_at?->toISOString(),
            ],

            // Actions (what user can do)
            'can_cancel' => $this->canBeCancelled(),
            'can_update' => $this->canBeUpdated(),
            'can_pay' => $this->payment_status === 'pending',
        ];
    }

    /**
     * Get human-readable status label
     */
    protected function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get human-readable payment status label
     */
    protected function getPaymentStatusLabel(): string
    {
        return match($this->payment_status) {
            'pending' => 'Payment Pending',
            'paid' => 'Paid',
            'failed' => 'Payment Failed',
            'refunded' => 'Refunded',
            default => ucfirst($this->payment_status),
        };
    }
}
