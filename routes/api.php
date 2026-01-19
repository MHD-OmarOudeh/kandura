<?php

use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\DesignController;
use App\Http\Controllers\Api\DesignOptionController;
use App\Http\Controllers\Api\MeasurementController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\PaymentController;


/*
|--------------------------------------------------------------------------
| API Routes - Stage 1 & 2
|--------------------------------------------------------------------------
*/

// ==========================================
// Public routes (No authentication)
// ==========================================
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// ==========================================
// Protected routes (Requires authentication)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {

    // ============ Auth Routes ============
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // ============ User Routes ============
    Route::get('/users/me', [UserController::class, 'show']);
    Route::put('/users/me', [UserController::class, 'update']);
    Route::post('/users/me', [UserController::class, 'update']); // for FormData with image

    // ============ Address Routes ============
    Route::apiResource('addresses', AddressController::class);

    // ============ Measurement Routes (Read Only) ============
    Route::get('/measurements', [MeasurementController::class, 'index']);
    Route::get('/measurements/{measurement}', [MeasurementController::class, 'show']);

    // ============ Design Routes ============
    // filter=my → user's own designs
    // filter=others → other users' designs
    Route::apiResource('designs', DesignController::class);

    // ============ Design Options Routes ============
    // All users can view (for creating designs)
    Route::get('/design-options', [DesignOptionController::class, 'index']);
    Route::get('/design-options/{designOption}', [DesignOptionController::class, 'show']);

    // Admin only routes for Design Options
    Route::middleware('permission:manage design options')->group(function () {
        Route::post('/design-options', [DesignOptionController::class, 'store']);
        Route::put('/design-options/{designOption}', [DesignOptionController::class, 'update']);
        Route::patch('/design-options/{designOption}', [DesignOptionController::class, 'update']);
        Route::delete('/design-options/{designOption}', [DesignOptionController::class, 'destroy']);
    });
        Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']); // Cancel order

    // Admin routes - manage all orders
    Route::middleware('permission:manage orders')->group(function () {
        Route::get('/orders/all', [OrderController::class, 'all']);
        Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus']);
    });
    // ============ Wallet Routes (Stage 4) ============

    // User routes - view wallet
    Route::get('/wallet', [WalletController::class, 'show']);
    Route::get('/wallet/transactions', [WalletController::class, 'transactions']);

    // Admin routes - manage wallet
    Route::middleware('permission:manage wallet')->group(function () {
        Route::post('/wallet/deposit', [WalletController::class, 'deposit']);
        Route::post('/wallet/withdraw', [WalletController::class, 'withdraw']);
    });

    // ============ Payment Routes (Stage 4) ============

    // Process payment for order
    Route::post('/orders/{order}/payment', [PaymentController::class, 'processPayment']);

    // Create payment intent (for Stripe)
    Route::post('/orders/{order}/payment-intent', [PaymentController::class, 'createPaymentIntent']);

    // Refund (Admin only)
    Route::middleware('permission:manage orders')->group(function () {
        Route::post('/orders/{order}/refund', [PaymentController::class, 'refund']);
    });
    // ============ Coupon Routes ============

    // User routes - view and validate coupons
    Route::get('/coupons/available', [CouponController::class, 'available']);
    Route::post('/coupons/validate', [CouponController::class, 'validate']);

    // Admin routes - manage coupons
    Route::middleware('permission:manage coupons')->group(function () {
        Route::get('/coupons', [CouponController::class, 'index']);
        Route::post('/coupons', [CouponController::class, 'store']);
        Route::get('/coupons/{coupon}', [CouponController::class, 'show']);
        Route::put('/coupons/{coupon}', [CouponController::class, 'update']);
        Route::patch('/coupons/{coupon}', [CouponController::class, 'update']);
        Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy']);
        Route::patch('/coupons/{coupon}/toggle', [CouponController::class, 'toggleStatus']);
        Route::get('/coupons/{coupon}/stats', [CouponController::class, 'stats']);
    });
});
Route::post('/webhooks/stripe', [PaymentController::class, 'stripeWebhook']);
