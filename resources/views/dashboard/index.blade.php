<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Kandura Store</title>
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

        /* Header */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 40px;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left h1 {
            font-size: 1.5em;
            font-weight: 700;
        }

        .header-left p {
            font-size: 0.85em;
            opacity: 0.95;
            margin-top: 3px;
        }

        .header-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .language-dropdown {
            position: relative;
        }

        .language-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .language-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }

        .language-menu {
            position: absolute;
            top: 110%;
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            min-width: 180px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .language-menu.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .language-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            color: #2d3748;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .language-menu a:hover {
            background: #f7fafc;
            padding-left: 20px;
        }

        .language-menu a.active {
            background: #eef2ff;
            color: #667eea;
            font-weight: 600;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            font-size: 1em;
        }

        .user-role {
            font-size: 0.8em;
            opacity: 0.85;
        }

        .btn-logout {
            padding: 8px 20px;
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9em;
            transition: all 0.3s ease;
            cursor: pointer;
            backdrop-filter: blur(10px);
        }

        .btn-logout:hover {
            background: white;
            color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        /* Main Content */
        .main-content {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 40px;
        }

        /* Dashboard Header */
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .dashboard-header h2 {
            font-size: 2.2em;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .dashboard-header p {
            opacity: 0.95;
            font-size: 1.1em;
            position: relative;
            z-index: 1;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border-left: 5px solid #667eea;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, transparent 100%);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.15);
        }

        .stat-card:nth-child(1) {
            border-left-color: #667eea;
        }

        .stat-card:nth-child(2) {
            border-left-color: #f093fb;
        }

        .stat-card:nth-child(3) {
            border-left-color: #4facfe;
        }

        .stat-card:nth-child(4) {
            border-left-color: #43e97b;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-icon {
            font-size: 2.5em;
            opacity: 0.8;
        }

        .stat-card h3 {
            font-size: 0.9em;
            color: #718096;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .stat-card .number {
            font-size: 2.8em;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .stat-card .description {
            font-size: 0.85em;
            color: #a0aec0;
        }

        /* Section Title */
        .section-title {
            font-size: 1.6em;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title::before {
            content: '';
            width: 5px;
            height: 35px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 3px;
        }

        /* Quick Actions */
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .action-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
            position: relative;
            overflow: hidden;
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .action-card:hover::before {
            transform: scaleX(1);
        }

        .action-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.2);
        }

        .action-icon {
            width: 65px;
            height: 65px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2em;
            color: white;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

        .action-card:hover .action-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .action-card h3 {
            font-size: 1.25em;
            color: #2d3748;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .action-card p {
            font-size: 0.9em;
            color: #718096;
            line-height: 1.6;
        }

        /* Recent Activity Section */
        .activity-section {
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .activity-list {
            list-style: none;
        }

        .activity-item {
            padding: 18px 0;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 18px;
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            background: #f7fafc;
            padding-left: 10px;
            border-radius: 8px;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #f0f4ff 0%, #e9ecff 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
            font-size: 1.3em;
            flex-shrink: 0;
        }

        .activity-content {
            flex: 1;
        }

        .activity-text {
            color: #2d3748;
            font-size: 0.95em;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .activity-time {
            color: #a0aec0;
            font-size: 0.8em;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state-icon {
            font-size: 4em;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 1.1em;
            color: #a0aec0;
        }

        /* No Permission State */
        .no-permission {
            background: white;
            padding: 60px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .no-permission-icon {
            font-size: 5em;
            margin-bottom: 20px;
            opacity: 0.4;
        }

        .no-permission h3 {
            font-size: 1.5em;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .no-permission p {
            color: #718096;
            margin-bottom: 5px;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            color: white;
            padding: 30px 40px;
            margin-top: 50px;
            text-align: center;
        }

        .footer p {
            opacity: 0.85;
            font-size: 0.9em;
            line-height: 1.8;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            .header-right {
                flex-direction: column;
                width: 100%;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .actions-grid {
                grid-template-columns: 1fr;
            }

            .main-content {
                padding: 0 20px;
            }

            .dashboard-header h2 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="header-left">
                <h1>Kandura Store Management</h1>
                <p>Khaleeji Kandura Design & Customer Management</p>
            </div>
            <div class="header-right">
                <!-- Language Dropdown -->
                <div class="language-dropdown">
                    <button class="language-btn" onclick="toggleLanguage()">
                        <span>🌐</span>
                        <span>{{ strtoupper(app()->getLocale()) }}</span>
                        <span>▼</span>
                    </button>
                    <div class="language-menu" id="languageMenu">
                        <a href="{{ route('language.switch', 'en') }}" class="{{ app()->getLocale() == 'en' ? 'active' : '' }}">
                            <span>🇬🇧</span>
                            <span>English</span>
                        </a>
                        <a href="{{ route('language.switch', 'ar') }}" class="{{ app()->getLocale() == 'ar' ? 'active' : '' }}">
                            <span>🇸🇦</span>
                            <span>العربية</span>
                        </a>
                    </div>
                </div>

                <div class="user-info">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">Administrator</div>
                </div>

                <form action="{{ route('dashboard.logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <h2>Welcome to Kandura Store Dashboard</h2>
            <p>Manage your Khaleeji Kandura designs, options, and customer operations</p>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            @can('manage all designs')
            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <h3>Total Designs</h3>
                        <div class="number">{{ \App\Models\Design::count() }}</div>
                        <div class="description">Kandura designs</div>
                    </div>
                    <div class="stat-icon">👔</div>
                </div>
            </div>
            @endcan

            @can('manage design options')
            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <h3>Design Options</h3>
                        <div class="number">{{ \App\Models\DesignOption::count() }}</div>
                        <div class="description">Colors, fabrics & styles</div>
                    </div>
                    <div class="stat-icon">🎨</div>
                </div>
            </div>
            @endcan

            @can('manage all addresses')
            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <h3>Total Addresses</h3>
                        <div class="number">{{ \App\Models\Address::count() }}</div>
                        <div class="description">Customer locations</div>
                    </div>
                    <div class="stat-icon">📍</div>
                </div>
            </div>
            @endcan
            @can('manage orders')
            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <h3>Total Orders</h3>
                        <div class="number">{{ \App\Models\Order::count() }}</div>
                        <div class="description">Customer orders</div>
                    </div>
                    <div class="stat-icon">📦</div>
                </div>
            </div>
            @endcan

            @can('manage coupons')
            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <h3>Active Coupons</h3>
                        <div class="number">{{ \App\Models\Coupon::where('is_active', true)->count() }}</div>
                        <div class="description">Discount coupons</div>
                    </div>
                    <div class="stat-icon">🎟️</div>
                </div>
            </div>
            @endcan
            @can('manage wallet')
            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <h3>Total Wallet Balance</h3>
                        <div class="number">{{ number_format(\App\Models\Wallet::sum('balance'), 2) }}</div>
                        <div class="description">SAR</div>
                    </div>
                    <div class="stat-icon">💰</div>
                </div>
            </div>
            @endcan



            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <h3>Total Users</h3>
                        <div class="number">{{ \App\Models\User::count() }}</div>
                        <div class="description">Registered customers</div>
                    </div>
                    <div class="stat-icon">👥</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <h2 class="section-title">Quick Actions</h2>
        <div class="actions-grid">
            @can('manage all designs')
            <a href="{{ route('dashboard.designs.index') }}" class="action-card">
                <div class="action-icon">👔</div>
                <h3>Kandura Designs</h3>
                <p>View and manage all customer Kandura designs</p>
            </a>
            @endcan

            @can('manage design options')
            <a href="{{ route('dashboard.design-options.index') }}" class="action-card">
                <div class="action-icon">🎨</div>
                <h3>Design Options</h3>
                <p>Manage colors, dome types, fabrics, and sleeve styles</p>
            </a>
            @endcan

            @can('manage all addresses')
            <a href="{{ route('dashboard.addresses.index') }}" class="action-card">
                <div class="action-icon">📍</div>
                <h3>Customer Addresses</h3>
                <p>Browse and manage delivery addresses</p>
            </a>
            @endcan
            <!-- In Quick Actions Grid - Add after existing action cards -->
            @can('manage orders')
            <a href="{{ route('dashboard.orders.index') }}" class="action-card">
                <div class="action-icon">📦</div>
                <h3>Orders Management</h3>
                <p>View, track and manage customer orders</p>
            </a>
            @endcan

            @can('manage coupons')
            <a href="{{ route('dashboard.coupons.index') }}" class="action-card">
                <div class="action-icon">🎟️</div>
                <h3>Coupons</h3>
                <p>Create and manage discount coupons</p>
            </a>
            @endcan

            @can('manage wallet')
            <a href="{{ route('dashboard.wallet.index') }}" class="action-card">
                <div class="action-icon">💰</div>
                <h3>Wallet Management</h3>
                <p>Manage user wallets and transactions</p>
            </a>
            @endcan
            {{-- <a href="#" class="action-card">
                <div class="action-icon">📊</div>
                <h3>Reports</h3>
                <p>Generate sales and design analytics reports</p>
            </a>

            <a href="#" class="action-card">
                <div class="action-icon">⚙️</div>
                <h3>Settings</h3>
                <p>Configure system settings and preferences</p>
            </a> --}}


        </div>

        <!-- Recent Activity -->
        <h2 class="section-title">Recent Activity</h2>
        <div class="activity-section">
            @php
                $recentActivities = collect();

                // Add recent designs
                if (auth()->user()->can('manage all designs')) {
                    $recentDesigns = \App\Models\Design::with('user')->latest()->take(3)->get();
                    foreach ($recentDesigns as $design) {
                        $recentActivities->push([
                            'icon' => '👔',
                            'text' => $design->user->name . ' created a new Kandura design',
                            'time' => $design->created_at->diffForHumans(),
                            'sort' => $design->created_at
                        ]);
                    }
                }

                // Add recent design options
                if (auth()->user()->can('manage design options')) {
                    $recentOptions = \App\Models\DesignOption::latest()->take(3)->get();
                    foreach ($recentOptions as $option) {
                        $recentActivities->push([
                            'icon' => '🎨',
                            'text' => 'New design option added: ' . $option->name,
                            'time' => $option->created_at->diffForHumans(),
                            'sort' => $option->created_at
                        ]);
                    }
                }

                // Add recent addresses
                if (auth()->user()->can('manage all addresses')) {
                    $recentAddresses = \App\Models\Address::with('user')->latest()->take(3)->get();
                    foreach ($recentAddresses as $address) {
                        $recentActivities->push([
                            'icon' => '📍',
                            'text' => $address->user->name . ' added a new delivery address',
                            'time' => $address->created_at->diffForHumans(),
                            'sort' => $address->created_at
                        ]);
                    }
                }

                // Add recent orders
                if (auth()->user()->can('manage orders')) {
                    $recentOrders = \App\Models\Order::with('user')->latest()->take(3)->get();
                    foreach ($recentOrders as $order) {
                        $recentActivities->push([
                            'icon' => '📦',
                            'text' => $order->user->name . ' placed order ' . $order->order_number,
                            'time' => $order->created_at->diffForHumans(),
                            'sort' => $order->created_at
                        ]);
                    }
                }

                // Add recent coupons
                if (auth()->user()->can('manage coupons')) {
                    $recentCoupons = \App\Models\Coupon::latest()->take(2)->get();
                    foreach ($recentCoupons as $coupon) {
                        $recentActivities->push([
                            'icon' => '🎟️',
                            'text' => 'New coupon created: ' . $coupon->code,
                            'time' => $coupon->created_at->diffForHumans(),
                            'sort' => $coupon->created_at
                        ]);
                    }
                }
            @endphp




            @if($recentActivities->count() > 0)
            <ul class="activity-list">
                @foreach($recentActivities as $activity)
                <li class="activity-item">
                    <div class="activity-icon">{{ $activity['icon'] }}</div>
                    <div class="activity-content">
                        <div class="activity-text">{{ $activity['text'] }}</div>
                        <div class="activity-time">{{ $activity['time'] }}</div>
                    </div>
                </li>
                @endforeach
            </ul>
            @else
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <p>No recent activity to display</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2025 Kandura Store - All Rights Reserved</p>
        <p>Khaleeji Kandura Design & Management System</p>
    </div>

    <script>
        function toggleLanguage() {
            const menu = document.getElementById('languageMenu');
            menu.classList.toggle('active');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.querySelector('.language-dropdown');
            const menu = document.getElementById('languageMenu');

            if (!dropdown.contains(event.target)) {
                menu.classList.remove('active');
            }
        });
    </script>
</body>
</html>
