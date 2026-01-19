<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet Management - Kandura Store</title>
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
            max-width: 1400px;
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
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 40px;
        }

        .page-header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .page-header h1 {
            font-size: 2em;
            color: #2d3748;
            margin-bottom: 10px;
        }

        /* Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #667eea;
        }

        .stat-card h3 {
            font-size: 0.85em;
            color: #718096;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .stat-card .number {
            font-size: 2em;
            font-weight: 700;
            color: #2d3748;
        }

        .stat-card .subtitle {
            font-size: 0.85em;
            color: #a0aec0;
            margin-top: 5px;
        }

        /* Filters */
        .filters-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .filter-group label {
            display: block;
            font-size: 0.9em;
            color: #4a5568;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.95em;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }

        /* Wallets Table */
        .wallets-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        th {
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9em;
        }

        td {
            padding: 18px 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        tbody tr {
            transition: all 0.3s ease;
        }

        tbody tr:hover {
            background: #f7fafc;
        }

        .balance {
            font-size: 1.2em;
            font-weight: 700;
        }

        .balance.positive {
            color: #27ae60;
        }

        .balance.zero {
            color: #a0aec0;
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85em;
            text-decoration: none;
            display: inline-block;
            margin: 0 3px;
            transition: all 0.3s ease;
        }

        .btn-view {
            background: #e9f3ff;
            color: #3182ce;
        }

        .btn-view:hover {
            background: #3182ce;
            color: white;
        }

        .btn-deposit {
            background: #e6f7ed;
            color: #27ae60;
        }

        .btn-deposit:hover {
            background: #27ae60;
            color: white;
        }

        .btn-withdraw {
            background: #fef5e7;
            color: #d68910;
        }

        .btn-withdraw:hover {
            background: #d68910;
            color: white;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 35px;
            border-radius: 15px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            font-size: 1.5em;
            color: #2d3748;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }

        .modal-body .form-group {
            margin-bottom: 20px;
        }

        .modal-body label {
            display: block;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .modal-body input,
        .modal-body select,
        .modal-body textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.95em;
        }

        .modal-body input:focus,
        .modal-body textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn-close {
            background: #e2e8f0;
            color: #4a5568;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>💰 Wallet Management</h1>
            <a href="{{ route('dashboard.index') }}" class="back-btn">← Back to Dashboard</a>
        </div>
    </div>

    <div class="main-content">
        <div class="page-header">
            <h1>User Wallets</h1>
            <p>Manage user wallet balances and transactions</p>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card" style="border-left-color: #27ae60;">
                <h3>Total Balance</h3>
                <div class="number">{{ number_format($totalBalance, 2) }} SAR</div>
                <div class="subtitle">Across all wallets</div>
            </div>
            <div class="stat-card" style="border-left-color: #3182ce;">
                <h3>Active Wallets</h3>
                <div class="number">{{ $activeWallets }}</div>
                <div class="subtitle">With balance > 0</div>
            </div>
            <div class="stat-card" style="border-left-color: #d68910;">
                <h3>Total Deposits</h3>
                <div class="number">{{ number_format($totalDeposits, 2) }} SAR</div>
                <div class="subtitle">All time</div>
            </div>
            <div class="stat-card" style="border-left-color: #764ba2;">
                <h3>Total Withdrawals</h3>
                <div class="number">{{ number_format($totalWithdrawals, 2) }} SAR</div>
                <div class="subtitle">All time</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-section">
            <form method="GET">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label>Search User</label>
                        <input type="text" name="search" placeholder="Name or email..." value="{{ request('search') }}">
                    </div>
                    <div class="filter-group">
                        <label>Min Balance</label>
                        <input type="number" name="min_balance" step="0.01" value="{{ request('min_balance') }}">
                    </div>
                    <div class="filter-group">
                        <label>Max Balance</label>
                        <input type="number" name="max_balance" step="0.01" value="{{ request('max_balance') }}">
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">🔍 Filter</button>
                    <a href="{{ route('dashboard.wallet.index') }}" class="btn btn-secondary">Clear</a>
                </div>
            </form>
        </div>

        <!-- Wallets Table -->
        <div class="wallets-table">
            @if($wallets->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Balance</th>
                        <th>Total Transactions</th>
                        <th>Last Activity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($wallets as $wallet)
                    <tr>
                        <td><strong>{{ $wallet->user->name }}</strong></td>
                        <td>{{ $wallet->user->email }}</td>
                        <td>
                            <span class="balance {{ $wallet->balance > 0 ? 'positive' : 'zero' }}">
                                {{ number_format($wallet->balance, 2) }} SAR
                            </span>
                        </td>
                        <td>{{ $wallet->transactions->count() }}</td>
                        <td>{{ $wallet->updated_at->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('dashboard.wallet.show', $wallet->user) }}" class="action-btn btn-view">View</a>
                            <button onclick="openDepositModal({{ $wallet->user->id }}, '{{ $wallet->user->name }}')"
                                    class="action-btn btn-deposit">
                                + Deposit
                            </button>
                            @if($wallet->balance > 0)
                            <button onclick="openWithdrawModal({{ $wallet->user->id }}, '{{ $wallet->user->name }}', {{ $wallet->balance }})"
                                    class="action-btn btn-withdraw">
                                - Withdraw
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="pagination">
                {{ $wallets->links() }}
            </div>
            @else
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 4em; margin-bottom: 15px; opacity: 0.5;">💰</div>
                <p style="font-size: 1.1em; color: #a0aec0;">No wallets found</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Deposit Modal -->
    <div id="depositModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">💰 Deposit Funds</div>
            <form action="{{ route('dashboard.wallet.deposit') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" id="deposit-user-id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>User</label>
                        <input type="text" id="deposit-user-name" readonly style="background: #f7fafc;">
                    </div>
                    <div class="form-group">
                        <label>Amount (SAR) <span style="color: #e74c3c;">*</span></label>
                        <input type="number" name="amount" step="0.01" min="0.01" required placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3" placeholder="Optional note..."></textarea>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn btn-primary">✅ Deposit</button>
                    <button type="button" class="btn btn-close" onclick="closeModal('depositModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Withdraw Modal -->
    <div id="withdrawModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">💸 Withdraw Funds</div>
            <form action="{{ route('dashboard.wallet.withdraw') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" id="withdraw-user-id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>User</label>
                        <input type="text" id="withdraw-user-name" readonly style="background: #f7fafc;">
                    </div>
                    <div class="form-group">
                        <label>Current Balance</label>
                        <input type="text" id="withdraw-balance" readonly style="background: #f7fafc; font-weight: 700; color: #27ae60;">
                    </div>
                    <div class="form-group">
                        <label>Amount (SAR) <span style="color: #e74c3c;">*</span></label>
                        <input type="number" name="amount" step="0.01" min="0.01" required placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3" placeholder="Optional note..."></textarea>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn btn-primary">✅ Withdraw</button>
                    <button type="button" class="btn btn-close" onclick="closeModal('withdrawModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDepositModal(userId, userName) {
            document.getElementById('deposit-user-id').value = userId;
            document.getElementById('deposit-user-name').value = userName;
            document.getElementById('depositModal').classList.add('active');
        }

        function openWithdrawModal(userId, userName, balance) {
            document.getElementById('withdraw-user-id').value = userId;
            document.getElementById('withdraw-user-name').value = userName;
            document.getElementById('withdraw-balance').value = balance.toFixed(2) + ' SAR';
            document.getElementById('withdrawModal').classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        // Close modal on outside click
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this.id);
                }
            });
        });
    </script>
</body>
</html>
