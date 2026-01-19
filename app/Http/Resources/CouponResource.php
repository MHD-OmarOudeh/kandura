<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'description' => $this->description,

            // Type and value
            'type' => $this->type,
            'value' => (float) $this->value,
            'formatted_value' => $this->formatted_value,

            // Usage limits
            'max_uses' => $this->max_uses,
            'used_count' => $this->used_count,
            'remaining_uses' => $this->remaining_uses,
            'max_uses_per_user' => $this->max_uses_per_user,

            // Validity
            'starts_at' => $this->starts_at?->toISOString(),
            'expires_at' => $this->expires_at?->toISOString(),
            'is_valid' => $this->isValid(),
            'is_active' => $this->is_active,

            // Restrictions
            'min_order_amount' => $this->min_order_amount ? (float) $this->min_order_amount : null,
            'is_restricted' => $this->isRestricted(),

            // Allowed users (admin only)
            'allowed_users' => $this->when(
                $request->user() && $request->user()->hasPermissionTo('manage coupons'),
                function() {
                    return $this->allowedUsers->map(function($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                        ];
                    });
                }
            ),

            // Statistics (admin only)
            'statistics' => $this->when(
                $request->user() && $request->user()->hasPermissionTo('manage coupons'),
                [
                    'unique_users_count' => $this->when(
                        $this->relationLoaded('users'),
                        fn() => $this->users->count()
                    ),
                ]
            ),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
