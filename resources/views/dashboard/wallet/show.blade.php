<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet - {{ $user->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 40px;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .back-btn {
            padding: 8px 20px;
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-weight: 600;
        }

        .main-content {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 40px;
        }

        /* User Header */
        .user-header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-info h1 {
            font-size: 2em;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .user-info p {
            color: #718096;
        }

        .wallet-balance {
            text-align: right;
        }

        .balance-label {
            color: #718096;
            font-size: 0.9em;
            margin-bottom: 5px;
        }

        .balance-amount {
            font-size: 3em;
            font-weight: 700;
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .stat-card h3 {
            font-size: 0.85em;
            color: #718096;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .stat-card .number {
            font-size: 1.8em;
            font-weight: 700;
            color: #2d3748;
        }

        .stat-card.green .number { color: #27ae60; }
        .stat-card.orange .number { color: #d68910; }
        .stat-card.blue .number { color: #3182ce; }

        /* Actions */
        .quick-actions {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .action-btn {
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-weight: 600;
            font-size: 1em;
        }

        .action-btn.deposit {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
        }

        .action-btn.withdraw {
            background: linear-gradient(135deg, #d68910 0%, #f39c12 100%);
            color: white;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Transactions */
        .transactions-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            font-size: 1.5em;
            color: #2d3748;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }

        .transaction-list {
            max-height: 600px;
            overflow-y: auto;
        }

        .transaction-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #f7fafc;
            transition: all 0.3s ease;
        }

        .transaction-item:hover {
            background: #f7fafc;
        }

        .transaction-info {
            flex: 1;
        }

        .transaction-type {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .transaction-desc {
            font-size: 0.85em;
            color: #718096;
            margin-bottom: 5px;
        }

        .transaction-time {
            font-size: 0.8em;
            color: #a0aec0;
        }

        .transaction-amount {
            font-size: 1.3em;
            font-weight: 700;
            margin-right: 20px;
        }

        .transaction-amount.credit {
            color: #27ae60;
        }

        .transaction-amount.debit {
            color: #e74c3c;
        }

        .transaction-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5em;
            margin-right: 15px;
        }

        .icon-deposit {
            background: #e6f7ed;
            color: #27ae60;
        }

        .icon-withdraw {
            background: #fef5e7;
            color: #d68910;
        }

        .icon-payment {
            background: #fdecea;
            color: #e74c3c;
        }

        .icon-refund {
            background: #e9f3ff;
            color: #3182ce;
        }

        .icon-reward {
            background: #f3e5f5;
            color: #9c27b0;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon {
            font-size: 4em;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        /* Filters */
        .transaction-filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
        }

        .filter-btn {
            padding: 8px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .filter-btn:hover,
        .filter-btn.active {
            border-color: #667eea;
            background: #eef2ff;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>💰 Wallet Details</h1>
            <a href="{{ route('dashboard.wallet.index') }}" class="back-btn">← Back to Wallets</a>
        </div>
    </div>

    <div class="main-content">
        <!-- User Header -->
        <div class="user-header">
            <div class="user-info">
                <h1>{{ $user->name }}</h1>
                <p>{{ $user->email }}</p>
            </div>
            <div class="wallet-balance">
                <div class="balance-label">Current Balance</div>
                <div class="balance-amount">{{ number_format($wallet->balance, 2) }} SAR</div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card green">
                <h3>Total Deposits</h3>
                <div class="number">{{ number_format($stats['total_deposits'], 2) }} SAR</div>
            </div>
            <div class="stat-card orange">
                <h3>Total Withdrawals</h3>
                <div class="number">{{ number_format($stats['total_withdrawals'], 2) }} SAR</div>
            </div>
            <div class="stat-card blue">
                <h3>Total Payments</h3>
                <div class="number">{{ number_format($stats['total_payments'], 2) }} SAR</div>
            </div>
            <div class="stat-card">
                <h3>Total Transactions</h3>
                <div class="number">{{ $stats['total_transactions'] }}</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <div class="actions-grid">
                <button onclick="openDepositModal()" class="action-btn deposit">
                    + Add Funds
                </button>
                @if($wallet->balance > 0)
                <button onclick="openWithdrawModal()" class="action-btn withdraw">
                    - Withdraw Funds
                </button>
                @endif
            </div>
        </div>

        <!-- Transactions -->
        <div class="transactions-section">
            <div class="section-title">Transaction History</div>

            <!-- Filters -->
            <div class="transaction-filters">
                <button class="filter-btn {{ !request('type') ? 'active' : '' }}"
                        onclick="window.location.href='{{ route('dashboard.wallet.show', $user) }}'">
                    All
                </button>
                <button class="filter-btn {{ request('type') == 'deposit' ? 'active' : '' }}"
                        onclick="window.location.href='{{ route('dashboard.wallet.show', ['user' => $user, 'type' => 'deposit']) }}'">
                    Deposits
                </button>
                <button class="filter-btn {{ request('type') == 'withdraw' ? 'active' : '' }}"
                        onclick="window.location.href='{{ route('dashboard.wallet.show', ['user' => $user, 'type' => 'withdraw']) }}'">
                    Withdrawals
                </button>
                <button class="filter-btn {{ request('type') == 'payment' ? 'active' : '' }}"
                        onclick="window.location.href='{{ route('dashboard.wallet.show', ['user' => $user, 'type' => 'payment']) }}'">
                    Payments
                </button>
                <button class="filter-btn {{ request('type') == 'refund' ? 'active' : '' }}"
                        onclick="window.location.href='{{ route('dashboard.wallet.show', ['user' => $user, 'type' => 'refund']) }}'">
                    Refunds
                </button>
            </div>

            @if($transactions->count() > 0)
            <div class="transaction-list">
                @foreach($transactions as $transaction)
                <div class="transaction-item">
                    <div class="transaction-icon icon-{{ $transaction->type }}">
                        @if($transaction->type == 'deposit')
                            💰
                        @elseif($transaction->type == 'withdraw')
                            💸
                        @elseif($transaction->type == 'payment')
                            🛒
                        @elseif($transaction->type == 'refund')
                            ↩️
                        @else
                            🎁
                        @endif
                    </div>

                    <div class="transaction-info">
                        <div class="transaction-type">{{ ucfirst($transaction->type) }}</div>
                        <div class="transaction-desc">{{ $transaction->description }}</div>
                        <div class="transaction-time">{{ $transaction->created_at->format('d M Y, H:i') }}</div>
                    </div>

                    <div class="transaction-amount {{ $transaction->isCredit() ? 'credit' : 'debit' }}">
                        {{ $transaction->isCredit() ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} SAR
                    </div>

                    <div style="text-align: right; min-width: 120px;">
                        <div style="font-size: 0.85em; color: #718096;">Balance</div>
                        <div style="font-weight: 600; color: #2d3748;">
                            {{ number_format($transaction->balance_after, 2) }} SAR
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div style="margin-top: 20px; text-align: center;">
                {{ $transactions->links() }}
            </div>
            @else
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <p style="color: #a0aec0;">No transactions found</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Same modals as index page -->
    <div id="depositModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 35px; border-radius: 15px; max-width: 500px; width: 90%;">
            <h2 style="font-size: 1.5em; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #e2e8f0;">💰 Deposit Funds</h2>
            <form action="{{ route('dashboard.wallet.deposit') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Amount (SAR)</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required
                           style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Description</label>
                    <textarea name="description" rows="3"
                              style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px;"></textarea>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" style="flex: 1; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">✅ Deposit</button>
                    <button type="button" onclick="closeModal('depositModal')" style="flex: 1; padding: 12px; background: #e2e8f0; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div id="withdrawModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 35px; border-radius: 15px; max-width: 500px; width: 90%;">
            <h2 style="font-size: 1.5em; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #e2e8f0;">💸 Withdraw Funds</h2>
            <form action="{{ route('dashboard.wallet.withdraw') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Current Balance</label>
                    <input type="text" value="{{ number_format($wallet->balance, 2) }} SAR" readonly
                           style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; background: #f7fafc; font-weight: 700; color: #27ae60;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Amount (SAR)</label>
                    <input type="number" name="amount" step="0.01" min="0.01" max="{{ $wallet->balance }}" required
                           style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Description</label>
                    <textarea name="description" rows="3"
                              style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px;"></textarea>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" style="flex: 1; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">✅ Withdraw</button>
                    <button type="button" onclick="closeModal('withdrawModal')" style="flex: 1; padding: 12px; background: #e2e8f0; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDepositModal() {
            document.getElementById('depositModal').style.display = 'flex';
        }

        function openWithdrawModal() {
            document.getElementById('withdrawModal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>
</body>
</html>
