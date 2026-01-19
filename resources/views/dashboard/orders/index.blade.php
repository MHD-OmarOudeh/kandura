<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Kandura Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: white;
            color: #667eea;
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

        .page-header p {
            color: #718096;
        }

        /* Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
            transition: all 0.3s ease;
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

        .btn-secondary:hover {
            background: #cbd5e0;
        }

        /* Orders Table */
        .orders-table {
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

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
            display: inline-block;
        }

        .badge-pending {
            background: #fef5e7;
            color: #d68910;
        }

        .badge-confirmed {
            background: #e8f4f8;
            color: #1a7f9c;
        }

        .badge-processing {
            background: #e9f3ff;
            color: #3182ce;
        }

        .badge-completed {
            background: #e6f7ed;
            color: #27ae60;
        }

        .badge-cancelled {
            background: #fdecea;
            color: #e74c3c;
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85em;
            text-decoration: none;
            display: inline-block;
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

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state-icon {
            font-size: 4em;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 20px;
        }

        .pagination a,
        .pagination span {
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
            color: #4a5568;
            border: 1px solid #e2e8f0;
        }

        .pagination .active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>📦 Orders Management</h1>
            <a href="{{ route('dashboard.index') }}" class="back-btn">← Back to Dashboard</a>
        </div>
    </div>

    <div class="main-content">
        <div class="page-header">
            <h1>All Orders</h1>
            <p>Manage and track customer orders</p>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card" style="border-left-color: #d68910;">
                <h3>Pending</h3>
                <div class="number">{{ $orders->where('status', 'pending')->count() }}</div>
            </div>
            <div class="stat-card" style="border-left-color: #1a7f9c;">
                <h3>Confirmed</h3>
                <div class="number">{{ $orders->where('status', 'confirmed')->count() }}</div>
            </div>
            <div class="stat-card" style="border-left-color: #3182ce;">
                <h3>Processing</h3>
                <div class="number">{{ $orders->where('status', 'processing')->count() }}</div>
            </div>
            <div class="stat-card" style="border-left-color: #27ae60;">
                <h3>Completed</h3>
                <div class="number">{{ $orders->where('status', 'completed')->count() }}</div>
            </div>
            <div class="stat-card" style="border-left-color: #e74c3c;">
                <h3>Cancelled</h3>
                <div class="number">{{ $orders->where('status', 'cancelled')->count() }}</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-section">
            <form method="GET" action="{{ route('dashboard.orders.index') }}">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label>Search</label>
                        <input type="text" name="search" placeholder="Order number or customer..." value="{{ request('search') }}">
                    </div>
                    <div class="filter-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Payment Method</label>
                        <select name="payment_method">
                            <option value="">All Methods</option>
                            @foreach($paymentMethods as $key => $label)
                                <option value="{{ $key }}" {{ request('payment_method') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Customer</label>
                        <select name="user_id">
                            <option value="">All Customers</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">🔍 Filter</button>
                    <a href="{{ route('dashboard.orders.index') }}" class="btn btn-secondary">Clear</a>
                </div>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="orders-table">
            @if($orders->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td><strong>{{ $order->order_number }}</strong></td>
                        <td>{{ $order->user->name }}</td>
                        <td><strong>{{ number_format($order->total, 2) }} SAR</strong></td>
                        <td>{{ ucfirst($order->payment_method) }}</td>
                        <td>
                            <span class="badge badge-{{ $order->status }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('dashboard.orders.show', $order) }}" class="action-btn btn-view">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="pagination">
                {{ $orders->links() }}
            </div>
            @else
            <div class="empty-state">
                <div class="empty-state-icon">📦</div>
                <p>No orders found</p>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
