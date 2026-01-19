<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'address_id',
        'subtotal',
        'tax',
        'shipping_cost',
        'discount',
        'total',
        'payment_method',
        'stripe_payment_intent_id',
        'payment_status',
        'paid_at',
        'status',
        'coupon_id',
        'notes',
        'confirmed_at',
        'processing_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'processing_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function designs()
    {
        return $this->belongsToMany(Design::class, 'order_items')
            ->withPivot(['quantity', 'unit_price', 'total_price', 'design_snapshot'])
            ->withTimestamps();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Uncomment when you create these models
    // public function invoice()
    // {
    //     return $this->hasOne(Invoice::class);
    // }

    // public function reviews()
    // {
    //     return $this->hasMany(Review::class);
    // }

    // ==========================================
    // Query Scopes
    // ==========================================

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('order_number', 'like', "%{$search}%")
              ->orWhereHas('user', function($userQuery) use ($search) {
                  $userQuery->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
              });
        });
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        return $query;
    }

    // ==========================================
    // Helper Methods
    // ==========================================

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function canBeUpdated(): bool
    {
        return in_array($this->status, ['pending', 'confirmed', 'processing']);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPaymentPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'KND';
        $timestamp = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -6));

        return "{$prefix}-{$timestamp}-{$random}";
    }

    /**
     * Calculate total items in order
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->orderItems()->sum('quantity');
    }
}
