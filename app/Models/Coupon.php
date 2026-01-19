<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'max_uses',
        'used_count',
        'max_uses_per_user',
        'starts_at',
        'expires_at',
        'min_order_amount',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'max_uses_per_user' => 'integer',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * Users who have used this coupon
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'coupon_user')
            ->withPivot(['order_id', 'used_at'])
            ->withTimestamps();
    }

    /**
     * Specific users allowed to use this coupon (optional restriction)
     */
    public function allowedUsers()
    {
        return $this->belongsToMany(User::class, 'coupon_allowed_users');
    }

    /**
     * Orders that used this coupon
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // ==========================================
    // Query Scopes
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        $now = now();
        return $query->where('is_active', true)
            ->where(function($q) use ($now) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', $now);
            })
            ->where('expires_at', '>', $now)
            ->where(function($q) {
                $q->whereNull('max_uses')
                  ->orWhereRaw('used_count < max_uses');
            });
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ==========================================
    // Validation Methods
    // ==========================================

    /**
     * Check if coupon is currently valid
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        // Check start date
        if ($this->starts_at && $this->starts_at->gt($now)) {
            return false;
        }

        // Check expiry date
        if ($this->expires_at->lt($now)) {
            return false;
        }

        // Check usage limit
        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * Check if user can use this coupon
     */
    public function canBeUsedBy(User $user): bool
    {
        // Check if coupon is valid
        if (!$this->isValid()) {
            return false;
        }

        // Check if restricted to specific users
        if ($this->allowedUsers()->exists()) {
            if (!$this->allowedUsers()->where('user_id', $user->id)->exists()) {
                return false;
            }
        }

        // Check if user already used this coupon
        $userUsageCount = $this->users()
            ->where('user_id', $user->id)
            ->count();

        if ($userUsageCount >= $this->max_uses_per_user) {
            return false;
        }

        return true;
    }

    /**
     * Check if coupon can be applied to order amount
     */
    public function canBeAppliedToAmount(float $amount): bool
    {
        // Check minimum order amount
        if ($this->min_order_amount && $amount < $this->min_order_amount) {
            return false;
        }

        // If fixed type, amount must be >= coupon value
        if ($this->type === 'fixed' && $amount < $this->value) {
            return false;
        }

        return true;
    }

    /**
     * Full validation for user and amount
     */
    public function validate(User $user, float $orderAmount): array
    {
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'This coupon is not active.'];
        }

        $now = now();

        if ($this->starts_at && $this->starts_at->gt($now)) {
            return ['valid' => false, 'message' => 'This coupon is not yet valid.'];
        }

        if ($this->expires_at->lt($now)) {
            return ['valid' => false, 'message' => 'This coupon has expired.'];
        }

        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return ['valid' => false, 'message' => 'This coupon has reached its usage limit.'];
        }

        // Check user-specific restrictions
        if ($this->allowedUsers()->exists()) {
            if (!$this->allowedUsers()->where('user_id', $user->id)->exists()) {
                return ['valid' => false, 'message' => 'You are not eligible to use this coupon.'];
            }
        }

        // Check if user already used
        $userUsageCount = $this->users()->where('user_id', $user->id)->count();
        if ($userUsageCount >= $this->max_uses_per_user) {
            return ['valid' => false, 'message' => 'You have already used this coupon.'];
        }

        // Check minimum amount
        if ($this->min_order_amount && $orderAmount < $this->min_order_amount) {
            return [
                'valid' => false,
                'message' => "Minimum order amount of {$this->min_order_amount} SAR required."
            ];
        }

        // Check fixed amount
        if ($this->type === 'fixed' && $orderAmount < $this->value) {
            return [
                'valid' => false,
                'message' => "Order amount must be at least {$this->value} SAR to use this coupon."
            ];
        }

        return ['valid' => true, 'message' => 'Coupon is valid.'];
    }

    // ==========================================
    // Helper Methods
    // ==========================================

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(float $orderAmount): float
    {
        if ($this->type === 'percentage') {
            return ($orderAmount * $this->value) / 100;
        } else {
            // Fixed amount
            return min($this->value, $orderAmount); // Don't exceed order amount
        }
    }

    /**
     * Mark coupon as used by user
     */
    public function markAsUsed(User $user, Order $order): void
    {
        $this->users()->attach($user->id, [
            'order_id' => $order->id,
            'used_at' => now(),
        ]);

        $this->increment('used_count');
    }

    /**
     * Check if coupon has remaining uses
     */
    public function hasRemainingUses(): bool
    {
        if ($this->max_uses === null) {
            return true; // Unlimited
        }

        return $this->used_count < $this->max_uses;
    }

    /**
     * Get remaining uses count
     */
    public function getRemainingUsesAttribute(): ?int
    {
        if ($this->max_uses === null) {
            return null; // Unlimited
        }

        return max(0, $this->max_uses - $this->used_count);
    }

    /**
     * Check if coupon is percentage type
     */
    public function isPercentage(): bool
    {
        return $this->type === 'percentage';
    }

    /**
     * Check if coupon is fixed amount type
     */
    public function isFixed(): bool
    {
        return $this->type === 'fixed';
    }

    /**
     * Format coupon value for display
     */
    public function getFormattedValueAttribute(): string
    {
        if ($this->isPercentage()) {
            return "{$this->value}%";
        } else {
            return "{$this->value} SAR";
        }
    }

    /**
     * Check if coupon is restricted to specific users
     */
    public function isRestricted(): bool
    {
        return $this->allowedUsers()->exists();
    }
}
