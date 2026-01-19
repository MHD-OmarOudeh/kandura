<?php

namespace App\Http\Services;

use App\Models\Coupon;
use App\Models\User;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class CouponService
{
    /**
     * Get all coupons with filters (Admin)
     */
    public function getAllCoupons(array $filters = []): LengthAwarePaginator
    {
        $query = Coupon::with(['allowedUsers'])->withCount('users');

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        // Filter by status
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // Filter by validity
        if (isset($filters['status'])) {
            if ($filters['status'] === 'valid') {
                $query->valid();
            } elseif ($filters['status'] === 'expired') {
                $query->where('expires_at', '<=', now());
            }
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get valid coupons (User can see)
     */
    public function getValidCoupons(User $user): LengthAwarePaginator
    {
        $query = Coupon::valid();

        // Only show non-restricted coupons OR coupons user is allowed to use
        $query->where(function($q) use ($user) {
            $q->whereDoesntHave('allowedUsers') // Public coupons
              ->orWhereHas('allowedUsers', function($allowedQuery) use ($user) {
                  $allowedQuery->where('user_id', $user->id);
              });
        });

        return $query->paginate(15);
    }

    /**
     * Create new coupon
     */
    public function createCoupon(array $data): Coupon
    {
        // Generate unique code if not provided
        if (empty($data['code'])) {
            $data['code'] = $this->generateUniqueCouponCode();
        } else {
            $data['code'] = strtoupper($data['code']);
        }

        // Set starts_at to now if not provided
        if (empty($data['starts_at'])) {
            $data['starts_at'] = now();
        }

        $coupon = Coupon::create($data);

        // Attach allowed users if specified
        if (!empty($data['allowed_user_ids'])) {
            $coupon->allowedUsers()->sync($data['allowed_user_ids']);
        }

        return $coupon->load('allowedUsers');
    }

    /**
     * Update coupon
     */
    public function updateCoupon(Coupon $coupon, array $data): Coupon
    {
        if (!empty($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        $coupon->update($data);

        // Update allowed users if specified
        if (isset($data['allowed_user_ids'])) {
            $coupon->allowedUsers()->sync($data['allowed_user_ids']);
        }

        return $coupon->fresh(['allowedUsers']);
    }

    /**
     * Delete coupon
     */
    public function deleteCoupon(Coupon $coupon): bool
    {
        // Check if coupon is being used in any orders
        if ($coupon->orders()->exists()) {
            throw new \Exception('Cannot delete coupon that has been used in orders');
        }

        return $coupon->delete();
    }

    /**
     * Validate coupon for user and order amount
     */
    public function validateCoupon(string $code, User $user, float $orderAmount): array
    {
        $coupon = Coupon::byCode($code)->first();

        if (!$coupon) {
            return [
                'valid' => false,
                'message' => 'Invalid coupon code.',
            ];
        }

        $validation = $coupon->validate($user, $orderAmount);

        if ($validation['valid']) {
            $discount = $coupon->calculateDiscount($orderAmount);
            return [
                'valid' => true,
                'message' => $validation['message'],
                'coupon' => $coupon,
                'discount' => $discount,
            ];
        }

        return $validation;
    }

    /**
     * Apply coupon to order
     */
    public function applyCouponToOrder(Coupon $coupon, User $user, Order $order): float
    {
        // Final validation
        $validation = $coupon->validate($user, $order->subtotal);

        if (!$validation['valid']) {
            throw new \Exception($validation['message']);
        }

        // Calculate discount
        $discount = $coupon->calculateDiscount($order->subtotal);

        // Mark as used
        $coupon->markAsUsed($user, $order);

        return $discount;
    }

    /**
     * Generate unique coupon code
     */
    protected function generateUniqueCouponCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Coupon::where('code', $code)->exists());

        return $code;
    }

    /**
     * Toggle coupon active status
     */
    public function toggleStatus(Coupon $coupon): Coupon
    {
        $coupon->update(['is_active' => !$coupon->is_active]);
        return $coupon->fresh();
    }

    /**
     * Get coupon usage statistics
     */
    public function getCouponStats(Coupon $coupon): array
    {
        return [
            'total_uses' => $coupon->used_count,
            'remaining_uses' => $coupon->remaining_uses,
            'unique_users' => $coupon->users()->distinct()->count(),
            'total_discount_given' => $coupon->orders()->sum('discount'),
            'is_valid' => $coupon->isValid(),
            'is_restricted' => $coupon->isRestricted(),
            'allowed_users_count' => $coupon->allowedUsers()->count(),
        ];
    }
}
