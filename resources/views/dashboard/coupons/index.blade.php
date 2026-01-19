<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coupons Management - Kandura Store</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            font-size: 2em;
            color: #2d3748;
        }

        .btn {
            padding: 12px 25px;
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

        /* Coupons Grid */
        .coupons-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        .coupon-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .coupon-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }

        .coupon-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.15);
        }

        .coupon-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
        }

        .coupon-code {
            font-size: 1.8em;
            font-weight: 700;
            color: #667eea;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }

        .coupon-type {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75em;
            font-weight: 600;
        }

        .type-percentage {
            background: #e9f3ff;
            color: #3182ce;
        }

        .type-fixed {
            background: #e6f7ed;
            color: #27ae60;
        }

        .coupon-value {
            font-size: 2.5em;
            font-weight: 700;
            color: #2d3748;
            margin: 15px 0;
        }

        .coupon-description {
            color: #718096;
            font-size: 0.9em;
            margin-bottom: 15px;
            min-height: 40px;
        }

        .coupon-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            padding: 15px 0;
            border-top: 1px solid #e2e8f0;
            font-size: 0.85em;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            color: #a0aec0;
            font-size: 0.85em;
            margin-bottom: 3px;
        }

        .info-value {
            color: #2d3748;
            font-weight: 600;
        }

        .coupon-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .action-btn {
            flex: 1;
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 0.85em;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-edit {
            background: #e9f3ff;
            color: #3182ce;
        }

        .btn-edit:hover {
            background: #3182ce;
            color: white;
        }

        .btn-toggle {
            background: #fef5e7;
            color: #d68910;
        }

        .btn-delete {
            background: #fdecea;
            color: #e74c3c;
        }

        .status-active {
            color: #27ae60;
            font-weight: 600;
        }

        .status-inactive {
            color: #e74c3c;
            font-weight: 600;
        }

        .expired-banner {
            background: #fdecea;
            color: #e74c3c;
            padding: 8px;
            border-radius: 6px;
            text-align: center;
            font-size: 0.85em;
            font-weight: 600;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>🎟️ Coupons Management</h1>
            <a href="{{ route('dashboard.index') }}" class="back-btn">← Back to Dashboard</a>
        </div>
    </div>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h1>All Coupons</h1>
                <p style="color: #718096; margin-top: 5px;">Create and manage discount coupons</p>
            </div>
            <a href="{{ route('dashboard.coupons.create') }}" class="btn btn-primary">+ Create Coupon</a>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card" style="border-left-color: #3182ce;">
                <h3>Total Coupons</h3>
                <div class="number">{{ $coupons->total() }}</div>
            </div>
            <div class="stat-card" style="border-left-color: #27ae60;">
                <h3>Active</h3>
                <div class="number">{{ $coupons->where('is_active', true)->count() }}</div>
            </div>
            <div class="stat-card" style="border-left-color: #d68910;">
                <h3>Percentage</h3>
                <div class="number">{{ $coupons->where('type', 'percentage')->count() }}</div>
            </div>
            <div class="stat-card" style="border-left-color: #764ba2;">
                <h3>Fixed</h3>
                <div class="number">{{ $coupons->where('type', 'fixed')->count() }}</div>
            </div>
        </div>

        <!-- Coupons Grid -->
        <div class="coupons-grid">
            @foreach($coupons as $coupon)
            <div class="coupon-card">
                @if($coupon->expires_at < now())
                    <div class="expired-banner">⏰ EXPIRED</div>
                @endif

                <div class="coupon-header">
                    <div class="coupon-code">{{ $coupon->code }}</div>
                    <span class="coupon-type type-{{ $coupon->type }}">
                        {{ $coupon->type == 'percentage' ? '%' : 'SAR' }}
                    </span>
                </div>

                <div class="coupon-value">
                    {{ $coupon->type == 'percentage' ? $coupon->value . '%' : $coupon->value . ' SAR' }}
                </div>

                <div class="coupon-description">
                    {{ $coupon->description ?? 'No description' }}
                </div>

                <div class="coupon-info">
                    <div class="info-item">
                        <span class="info-label">Uses</span>
                        <span class="info-value">
                            {{ $coupon->used_count }} / {{ $coupon->max_uses ?? '∞' }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Expires</span>
                        <span class="info-value">{{ $coupon->expires_at->format('d M Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status</span>
                        <span class="info-value {{ $coupon->is_active ? 'status-active' : 'status-inactive' }}">
                            {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Min Order</span>
                        <span class="info-value">{{ $coupon->min_order_amount ?? 'None' }}</span>
                    </div>
                </div>

                <div class="coupon-actions">
                    <a href="{{ route('dashboard.coupons.edit', $coupon) }}" class="action-btn btn-edit">Edit</a>

                    <form action="{{ route('dashboard.coupons.toggle', $coupon) }}" method="POST" style="flex: 1;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="action-btn btn-toggle" style="width: 100%;">
                            {{ $coupon->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>

                    <form action="{{ route('dashboard.coupons.destroy', $coupon) }}" method="POST" style="flex: 1;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn btn-delete"
                                onclick="return confirm('Delete this coupon?')" style="width: 100%;">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        @if($coupons->isEmpty())
        <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 15px;">
            <div style="font-size: 4em; margin-bottom: 15px; opacity: 0.5;">🎟️</div>
            <p style="font-size: 1.1em; color: #a0aec0;">No coupons yet. Create your first coupon!</p>
        </div>
        @endif

        <!-- Pagination -->
        <div style="margin-top: 30px;">
            {{ $coupons->links() }}
        </div>
    </div>
</body>
</html>
