<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Pagination\LengthAwarePaginator;

class WalletService
{
    /**
     * Get or create user wallet
     */
    public function getOrCreateWallet(User $user): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );
    }

    /**
     * Get wallet with transactions
     */
    public function getWalletDetails(User $user): Wallet
    {
        return $this->getOrCreateWallet($user)->load('transactions');
    }

    /**
     * Get wallet transactions with filters
     */
    public function getTransactions(User $user, array $filters = []): LengthAwarePaginator
    {
        $wallet = $this->getOrCreateWallet($user);

        $query = $wallet->transactions()
            ->with(['order', 'performedBy']);

        // Filter by type
        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        // Date range
        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Admin: Add funds to user wallet
     */
    public function adminDeposit(User $user, float $amount, User $admin, string $description = null): WalletTransaction
    {
        if ($amount <= 0) {
            throw new \Exception('Amount must be greater than zero');
        }

        $wallet = $this->getOrCreateWallet($user);

        return $wallet->deposit(
            $amount,
            $description ?? "Funds added by admin ({$admin->name})",
            $admin
        );
    }

    /**
     * Admin: Withdraw funds from user wallet
     */
    public function adminWithdraw(User $user, float $amount, User $admin, string $description = null): WalletTransaction
    {
        if ($amount <= 0) {
            throw new \Exception('Amount must be greater than zero');
        }

        $wallet = $this->getOrCreateWallet($user);

        if (!$wallet->hasSufficientBalance($amount)) {
            throw new \Exception('Insufficient wallet balance');
        }

        return $wallet->withdraw(
            $amount,
            $description ?? "Funds withdrawn by admin ({$admin->name})",
            $admin
        );
    }

    /**
     * Get wallet statistics
     */
    public function getWalletStats(User $user): array
    {
        $wallet = $this->getOrCreateWallet($user);
        $transactions = $wallet->transactions;

        return [
            'current_balance' => (float) $wallet->balance,
            'total_deposits' => (float) $transactions->where('type', 'deposit')->sum('amount'),
            'total_withdrawals' => (float) $transactions->where('type', 'withdraw')->sum('amount'),
            'total_payments' => (float) $transactions->where('type', 'payment')->sum('amount'),
            'total_refunds' => (float) $transactions->where('type', 'refund')->sum('amount'),
            'total_rewards' => (float) $transactions->where('type', 'reward')->sum('amount'),
            'total_transactions' => $transactions->count(),
        ];
    }

    /**
     * Check if user can afford amount
     */
    public function canAfford(User $user, float $amount): bool
    {
        $wallet = $this->getOrCreateWallet($user);
        return $wallet->hasSufficientBalance($amount);
    }
}
