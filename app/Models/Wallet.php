<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class)->orderBy('created_at', 'desc');
    }

    // ==========================================
    // Helper Methods
    // ==========================================

    /**
     * Check if wallet has sufficient balance
     */
    public function hasSufficientBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * Add funds to wallet
     */
    public function deposit(float $amount, string $description = null, ?User $performedBy = null, ?int $orderId = null)
    {
        $balanceBefore = $this->balance;
        $this->balance += $amount;
        $this->save();

        return $this->transactions()->create([
            'type' => 'deposit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'description' => $description ?? 'Funds added to wallet',
            'performed_by' => $performedBy?->id,
            'order_id' => $orderId,
        ]);
    }

    /**
     * Withdraw funds from wallet
     */
    public function withdraw(float $amount, string $description = null, ?User $performedBy = null, ?int $orderId = null)
    {
        if (!$this->hasSufficientBalance($amount)) {
            throw new \Exception('Insufficient wallet balance');
        }

        $balanceBefore = $this->balance;
        $this->balance -= $amount;
        $this->save();

        return $this->transactions()->create([
            'type' => 'withdraw',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'description' => $description ?? 'Funds withdrawn from wallet',
            'performed_by' => $performedBy?->id,
            'order_id' => $orderId,
        ]);
    }

    /**
     * Process payment from wallet
     */
    public function processPayment(float $amount, int $orderId, string $description = null)
    {
        if (!$this->hasSufficientBalance($amount)) {
            throw new \Exception('Insufficient wallet balance');
        }

        $balanceBefore = $this->balance;
        $this->balance -= $amount;
        $this->save();

        return $this->transactions()->create([
            'type' => 'payment',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'description' => $description ?? "Payment for order #{$orderId}",
            'order_id' => $orderId,
        ]);
    }

    /**
     * Process refund to wallet
     */
    public function refund(float $amount, int $orderId, string $description = null)
    {
        $balanceBefore = $this->balance;
        $this->balance += $amount;
        $this->save();

        return $this->transactions()->create([
            'type' => 'refund',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'description' => $description ?? "Refund for order #{$orderId}",
            'order_id' => $orderId,
        ]);
    }

    /**
     * Add reward to wallet
     */
    public function addReward(float $amount, string $description = null)
    {
        $balanceBefore = $this->balance;
        $this->balance += $amount;
        $this->save();

        return $this->transactions()->create([
            'type' => 'reward',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'description' => $description ?? 'Reward added to wallet',
        ]);
    }
}
