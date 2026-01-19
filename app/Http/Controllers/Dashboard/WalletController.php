<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\WalletDepositRequest;
use App\Http\Requests\WalletWithdrawRequest;
use App\Http\Services\WalletService;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Display listing of all wallets
     */
    public function index(Request $request)
    {
        $query = Wallet::with(['user', 'transactions']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Balance filters
        if ($request->filled('min_balance')) {
            $query->where('balance', '>=', $request->min_balance);
        }
        if ($request->filled('max_balance')) {
            $query->where('balance', '<=', $request->max_balance);
        }

        $wallets = $query->orderBy('balance', 'desc')->paginate(15);

        // Statistics
        $totalBalance = Wallet::sum('balance');
        $activeWallets = Wallet::where('balance', '>', 0)->count();
        $totalDeposits = WalletTransaction::where('type', 'deposit')->sum('amount');
        $totalWithdrawals = WalletTransaction::where('type', 'withdraw')->sum('amount');

        return view('dashboard.wallet.index', compact(
            'wallets',
            'totalBalance',
            'activeWallets',
            'totalDeposits',
            'totalWithdrawals'
        ));
    }

    /**
     * Display specific user wallet
     */
    public function show(Request $request, User $user)
    {
        $wallet = $this->walletService->getWalletDetails($user);
        $stats = $this->walletService->getWalletStats($user);

        // Get transactions with optional type filter
        $filters = $request->only(['type', 'per_page']);
        $transactions = $this->walletService->getTransactions($user, $filters);

        return view('dashboard.wallet.show', compact(
            'user',
            'wallet',
            'stats',
            'transactions'
        ));
    }

    /**
     * Deposit funds to user wallet
     */
    public function deposit(WalletDepositRequest $request)
    {
        try {
            $admin = $request->user();
            $targetUser = User::findOrFail($request->user_id);

            $this->walletService->adminDeposit(
                $targetUser,
                $request->amount,
                $admin,
                $request->description
            );

            return redirect()
                ->back()
                ->with('success', "Successfully deposited {$request->amount} SAR to {$targetUser->name}'s wallet");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Deposit failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Withdraw funds from user wallet
     */
    public function withdraw(WalletWithdrawRequest $request)
    {
        try {
            $admin = $request->user();
            $targetUser = User::findOrFail($request->user_id);

            $this->walletService->adminWithdraw(
                $targetUser,
                $request->amount,
                $admin,
                $request->description
            );

            return redirect()
                ->back()
                ->with('success', "Successfully withdrew {$request->amount} SAR from {$targetUser->name}'s wallet");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Withdrawal failed: ' . $e->getMessage()]);
        }
    }
}
