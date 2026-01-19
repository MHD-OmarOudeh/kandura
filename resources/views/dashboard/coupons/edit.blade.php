<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Coupon - {{ $coupon->code }}</title>
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

        .form-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .form-header {
            margin-bottom: 35px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
        }

        .form-header h1 {
            font-size: 2em;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .coupon-code-badge {
            display: inline-block;
            padding: 8px 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1.2em;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .form-grid.full {
            grid-template-columns: 1fr;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            font-size: 0.95em;
        }

        .form-group label .required {
            color: #e74c3c;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.95em;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group small {
            color: #a0aec0;
            font-size: 0.85em;
            margin-top: 5px;
        }

        .section-header {
            font-size: 1.3em;
            color: #2d3748;
            margin: 30px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-header::before {
            content: '';
            width: 4px;
            height: 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .radio-option:hover {
            border-color: #667eea;
            background: #f7fafc;
        }

        .radio-option input[type="radio"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .radio-option.selected {
            border-color: #667eea;
            background: #eef2ff;
        }

        .user-selection {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            max-height: 300px;
            overflow-y: auto;
        }

        .user-item {
            padding: 10px;
            border-bottom: 1px solid #f7fafc;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .user-item label {
            cursor: pointer;
            flex: 1;
            margin: 0 !important;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 35px;
            padding-top: 25px;
            border-top: 2px solid #e2e8f0;
        }

        .btn {
            padding: 14px 30px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 1em;
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

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .alert-danger {
            background: #fdecea;
            color: #e74c3c;
            border-left: 4px solid #e74c3c;
        }

        .info-box {
            background: #eef2ff;
            border-left: 4px solid #667eea;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .info-box strong {
            color: #667eea;
            display: block;
            margin-bottom: 5px;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-box {
            background: #f7fafc;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .stat-box .label {
            color: #718096;
            font-size: 0.85em;
            margin-bottom: 5px;
        }

        .stat-box .value {
            color: #2d3748;
            font-size: 1.5em;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>🎟️ Edit Coupon</h1>
            <a href="{{ route('dashboard.coupons.index') }}" class="back-btn">← Back to Coupons</a>
        </div>
    </div>

    <div class="main-content">
        <div class="form-container">
            <div class="form-header">
                <div class="coupon-code-badge">{{ $coupon->code }}</div>
                <h1>Edit Coupon Details</h1>
            </div>

            @if($errors->any())
            <div class="alert alert-danger">
                <strong>⚠️ Please fix the following errors:</strong>
                <ul style="margin: 10px 0 0 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Usage Statistics -->
            <div class="stats-row">
                <div class="stat-box">
                    <div class="label">Total Uses</div>
                    <div class="value">{{ $coupon->used_count }}</div>
                </div>
                <div class="stat-box">
                    <div class="label">Remaining</div>
                    <div class="value">{{ $coupon->remaining_uses ?? '∞' }}</div>
                </div>
                <div class="stat-box">
                    <div class="label">Unique Users</div>
                    <div class="value">{{ $coupon->users()->distinct()->count() }}</div>
                </div>
            </div>

            <form action="{{ route('dashboard.coupons.update', $coupon) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Basic Info -->
                <div class="section-header">📝 Basic Information</div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="code">Coupon Code</label>
                        <input type="text"
                               id="code"
                               name="code"
                               value="{{ old('code', $coupon->code) }}"
                               style="text-transform: uppercase;"
                               readonly>
                        <small>Code cannot be changed after creation</small>
                    </div>

                    <div class="form-group">
                        <label for="expires_at">
                            Expiration Date <span class="required">*</span>
                        </label>
                        <input type="datetime-local"
                               id="expires_at"
                               name="expires_at"
                               value="{{ old('expires_at', $coupon->expires_at->format('Y-m-d\TH:i')) }}"
                               required>
                    </div>
                </div>

                <div class="form-grid full">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description"
                                  name="description">{{ old('description', $coupon->description) }}</textarea>
                    </div>
                </div>

                <!-- Discount Settings -->
                <div class="section-header">💰 Discount Settings</div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Discount Type</label>
                        <div class="radio-group">
                            <label class="radio-option {{ $coupon->type == 'percentage' ? 'selected' : '' }}">
                                <input type="radio"
                                       name="type"
                                       value="percentage"
                                       {{ old('type', $coupon->type) == 'percentage' ? 'checked' : '' }}
                                       disabled>
                                <span>📊 Percentage (%)</span>
                            </label>
                            <label class="radio-option {{ $coupon->type == 'fixed' ? 'selected' : '' }}">
                                <input type="radio"
                                       name="type"
                                       value="fixed"
                                       {{ old('type', $coupon->type) == 'fixed' ? 'checked' : '' }}
                                       disabled>
                                <span>💵 Fixed Amount (SAR)</span>
                            </label>
                        </div>
                        <small>Type cannot be changed after creation</small>
                    </div>

                    <div class="form-group">
                        <label for="value">
                            Discount Value <span class="required">*</span>
                        </label>
                        <input type="number"
                               id="value"
                               name="value"
                               step="0.01"
                               min="0.01"
                               value="{{ old('value', $coupon->value) }}"
                               required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="min_order_amount">Minimum Order Amount (SAR)</label>
                        <input type="number"
                               id="min_order_amount"
                               name="min_order_amount"
                               step="0.01"
                               min="0"
                               value="{{ old('min_order_amount', $coupon->min_order_amount) }}">
                    </div>

                    <div class="form-group">
                        <label for="starts_at">Start Date</label>
                        <input type="datetime-local"
                               id="starts_at"
                               name="starts_at"
                               value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\TH:i')) }}">
                    </div>
                </div>

                <!-- Usage Limits -->
                <div class="section-header">🔢 Usage Limits</div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="max_uses">Maximum Total Uses</label>
                        <input type="number"
                               id="max_uses"
                               name="max_uses"
                               min="1"
                               value="{{ old('max_uses', $coupon->max_uses) }}">
                        <small>Current uses: {{ $coupon->used_count }}</small>
                    </div>

                    <div class="form-group">
                        <label for="max_uses_per_user">Max Uses Per User</label>
                        <input type="number"
                               id="max_uses_per_user"
                               name="max_uses_per_user"
                               min="1"
                               value="{{ old('max_uses_per_user', $coupon->max_uses_per_user) }}"
                               required>
                    </div>
                </div>

                <!-- User Restrictions -->
                <div class="section-header">👥 User Restrictions</div>

                <div class="form-group">
                    <label>
                        <input type="checkbox"
                               id="toggle-users"
                               onclick="toggleUserSelection()"
                               {{ $coupon->allowedUsers->count() > 0 ? 'checked' : '' }}>
                        Restrict to specific users
                    </label>
                </div>

                <div id="user-selection-container" style="display: {{ $coupon->allowedUsers->count() > 0 ? 'block' : 'none' }}; margin-top: 15px;">
                    <div class="user-selection">
                        @foreach($users as $user)
                        <div class="user-item">
                            <input type="checkbox"
                                   name="allowed_user_ids[]"
                                   value="{{ $user->id }}"
                                   id="user-{{ $user->id }}"
                                   {{ $coupon->allowedUsers->contains($user->id) || in_array($user->id, old('allowed_user_ids', [])) ? 'checked' : '' }}>
                            <label for="user-{{ $user->id }}">
                                <strong>{{ $user->name }}</strong> - {{ $user->email }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Status -->
                <div class="section-header">⚙️ Status</div>

                <div class="form-group">
                    <label>
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                        Active
                    </label>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Update Coupon</button>
                    <a href="{{ route('dashboard.coupons.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleUserSelection() {
            const container = document.getElementById('user-selection-container');
            const checkbox = document.getElementById('toggle-users');
            container.style.display = checkbox.checked ? 'block' : 'none';

            if (!checkbox.checked) {
                document.querySelectorAll('input[name="allowed_user_ids[]"]').forEach(input => {
                    input.checked = false;
                });
            }
        }
        document.addEventListener('DOMContentLoaded', function () {
            const expires = document.getElementById('expires_at');

            // حط وقت تلقائي أول ما الصفحة تفتح
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            expires.value = now.toISOString().slice(0, 16);

            // لو المستخدم اختار تاريخ بدون وقت
            expires.addEventListener('change', function () {
                if (this.value.length === 10) {
                    this.value += 'T23:59';
                }
            });
        });
    </script>
</body>
</html>
