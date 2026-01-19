<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;

class CouponPolicy
{
    /**
     * Determine if user can view any coupons (Admin only)
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage coupons');
    }

    /**
     * Determine if user can view the coupon
     */
    public function view(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('manage coupons');
    }

    /**
     * Determine if user can create coupons (Admin only)
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage coupons');
    }

    /**
     * Determine if user can update the coupon (Admin only)
     */
    public function update(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('manage coupons');
    }

    /**
     * Determine if user can delete the coupon (Admin only)
     */
    public function delete(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('manage coupons');
    }
}
