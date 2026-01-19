<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\AuthController as DashboardAuthController;
use App\Http\Controllers\Dashboard\AddressController as DashboardAddressController;
use App\Http\Controllers\Dashboard\DesignOptionController as DashboardDesignOptionController;
use App\Http\Controllers\Dashboard\DesignController as DashboardDesignController;
use App\Http\Controllers\Dashboard\OrderController as DashboardOrderController;
use App\Http\Controllers\Dashboard\CouponController as DashboardCouponController;
use App\Http\Controllers\Dashboard\WalletController as DashboardWalletController;

/*
|--------------------------------------------------------------------------
| Web Routes - Dashboard (Stage 1 & 2)
|--------------------------------------------------------------------------
*/

// Welcome page
Route::get('/', function () {
    return view('');
});

// Dashboard routes
Route::prefix('dashboard')->name('dashboard.')->group(function () {

    // Guest routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [DashboardAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [DashboardAuthController::class, 'login'])->name('login.post');
    });

    // Authenticated routes
    Route::middleware(['auth', 'permission:access dashboard'])->group(function () {

        // Dashboard Home
        Route::get('/', function () {
            return view('dashboard.index');
        })->name('index');

        // Logout
        Route::post('/logout', [DashboardAuthController::class, 'logout'])->name('logout');

        // Address Management
        Route::middleware('permission:manage all addresses')->group(function () {
            Route::resource('addresses', DashboardAddressController::class)->names('addresses');
        });

        // Design Options
        Route::middleware('permission:manage design options')->group(function () {
            Route::post('design-options/create', [DashboardDesignOptionController::class, 'store'])->name("design-options.create");
            Route::resource('design-options', DashboardDesignOptionController::class)->names('design-options');
        });

        // Designs
        Route::middleware('permission:manage all designs')->group(function () {
            Route::get('/designs', [DashboardDesignController::class, 'index'])->name('designs.index');
            Route::get('/designs/{design}', [DashboardDesignController::class, 'show'])->name('designs.show');
        });

        // Orders
        Route::middleware('permission:manage orders')->group(function () {
            Route::get('/orders', [DashboardOrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [DashboardOrderController::class, 'show'])->name('orders.show');
            Route::put('/orders/{order}/status', [DashboardOrderController::class, 'updateStatus'])->name('orders.update-status');
            Route::post('/orders/{order}/cancel', [DashboardOrderController::class, 'cancel'])->name('orders.cancel');
        });

        // Coupons
        Route::middleware('permission:manage coupons')->group(function () {
            Route::resource('coupons', DashboardCouponController::class)->names('coupons');
            Route::patch('/coupons/{coupon}/toggle', [DashboardCouponController::class, 'toggleStatus'])->name('coupons.toggle');
        });
        // Wallet Management
        Route::middleware('permission:manage wallet')->group(function () {
            Route::get('/wallet', [DashboardWalletController::class, 'index'])->name('wallet.index');
            Route::get('/wallet/users/{user}', [DashboardWalletController::class, 'show'])->name('wallet.show');
            Route::post('/wallet/deposit', [DashboardWalletController::class, 'deposit'])->name('wallet.deposit');
            Route::post('/wallet/withdraw', [DashboardWalletController::class, 'withdraw'])->name('wallet.withdraw');
        });

    });
});

// Language Switcher Route
Route::get('/language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('language.switch');
