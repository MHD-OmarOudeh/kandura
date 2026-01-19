<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WalletDepositRequest;
use App\Http\Requests\WalletWithdrawRequest;
use App\Http\Resources\WalletResource;
use App\Http\Resources\WalletTransactionResource;
use App\Http\Services\WalletService;
use App\Models\User;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Get user wallet details
     * GET /api/wallet
     */
    public function show(Request $request)
    {
        try {
            $user = $request->user();
            $wallet = $this->walletService->getWalletDetails($user);
            $stats = $this->walletService->getWalletStats($user);

            return $this->success([
                'wallet' => new WalletResource($wallet),
                'stats' => $stats,
            ], 'Wallet retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve wallet', $e->getMessage(), 500);
        }
    }

    /**
     * Get wallet transactions
     * GET /api/wallet/transactions
     */
    public function transactions(Request $request)
    {
        try {
            $user = $request->user();
            $filters = $request->only(['type', 'start_date', 'end_date', 'sort_by', 'sort_direction', 'per_page']);

            $transactions = $this->walletService->getTransactions($user, $filters);

            return $this->success(
                WalletTransactionResource::collection($transactions)->response()->getData(true),
                'Transactions retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve transactions', $e->getMessage(), 500);
        }
    }

    /**
     * Admin: Add funds to user wallet
     * POST /api/wallet/deposit
     */
    public function deposit(WalletDepositRequest $request)
    {
        try {
            $this->authorize('manageWallet', User::class);

            $admin = $request->user();
            $targetUser = User::findOrFail($request->user_id);

            $transaction = $this->walletService->adminDeposit(
                $targetUser,
                $request->amount,
                $admin,
                $request->description
            );

            return $this->success(
                new WalletTransactionResource($transaction),
                'Funds added successfully',
                201
            );
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbidden('Unauthorized access');
        } catch (\Exception $e) {
            return $this->error('Failed to add funds', $e->getMessage(), 500);
        }
    }

    /**
     * Admin: Withdraw funds from user wallet
     * POST /api/wallet/withdraw
     */
    public function withdraw(WalletWithdrawRequest $request)
    {
        try {
            $this->authorize('manageWallet', User::class);

            $admin = $request->user();
            $targetUser = User::findOrFail($request->user_id);

            $transaction = $this->walletService->adminWithdraw(
                $targetUser,
                $request->amount,
                $admin,
                $request->description
            );

            return $this->success(
                new WalletTransactionResource($transaction),
                'Funds withdrawn successfully'
            );
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbidden('Unauthorized access');
        } catch (\Exception $e) {
            return $this->error('Failed to withdraw funds', $e->getMessage(), 500);
        }
    }
}
