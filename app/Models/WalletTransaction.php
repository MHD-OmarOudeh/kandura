<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'order_id',
        'performed_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    // ==========================================
    // Query Scopes
    // ==========================================

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDeposits($query)
    {
        return $query->where('type', 'deposit');
    }

    public function scopeWithdrawals($query)
    {
        return $query->where('type', 'withdraw');
    }

    public function scopePayments($query)
    {
        return $query->where('type', 'payment');
    }

    public function scopeRefunds($query)
    {
        return $query->where('type', 'refund');
    }

    public function scopeRewards($query)
    {
        return $query->where('type', 'reward');
    }

    // ==========================================
    // Helper Methods
    // ==========================================

    public function isDeposit(): bool
    {
        return $this->type === 'deposit';
    }

    public function isWithdrawal(): bool
    {
        return $this->type === 'withdraw';
    }

    public function isPayment(): bool
    {
        return $this->type === 'payment';
    }

    public function isRefund(): bool
    {
        return $this->type === 'refund';
    }

    public function isReward(): bool
    {
        return $this->type === 'reward';
    }

    /**
     * Get transaction type label
     */
    public function getTypeLabel(): string
    {
        return match($this->type) {
            'deposit' => 'Deposit',
            'withdraw' => 'Withdrawal',
            'payment' => 'Payment',
            'refund' => 'Refund',
            'reward' => 'Reward',
            default => ucfirst($this->type),
        };
    }

    /**
     * Check if transaction increased balance
     */
    public function isCredit(): bool
    {
        return in_array($this->type, ['deposit', 'refund', 'reward']);
    }

    /**
     * Check if transaction decreased balance
     */
    public function isDebit(): bool
    {
        return in_array($this->type, ['withdraw', 'payment']);
    }
}
