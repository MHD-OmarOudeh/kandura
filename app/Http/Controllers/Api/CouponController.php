<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Http\Requests\ValidateCouponRequest;
use App\Http\Resources\CouponResource;
use App\Http\Services\CouponService;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    protected CouponService $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    /**
     * Get all coupons (Admin)
     * GET /api/coupons
     */
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', Coupon::class);

            $filters = $request->only([
                'search',
                'type',
                'is_active',
                'status',
                'sort_by',
                'sort_direction',
                'per_page'
            ]);

            $coupons = $this->couponService->getAllCoupons($filters);

            return $this->success(
                CouponResource::collection($coupons)->response()->getData(true),
                'Coupons retrieved successfully'
            );
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbidden('Unauthorized access');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve coupons', $e->getMessage(), 500);
        }
    }

    /**
     * Get valid coupons (User)
     * GET /api/coupons/available
     */
    public function available(Request $request)
    {
        try {
            $user = $request->user();
            $coupons = $this->couponService->getValidCoupons($user);

            return $this->success(
                CouponResource::collection($coupons)->response()->getData(true),
                'Available coupons retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve coupons', $e->getMessage(), 500);
        }
    }

    /**
     * Create new coupon (Admin)
     * POST /api/coupons
     */
    public function store(StoreCouponRequest $request)
    {
        try {
            $coupon = $this->couponService->createCoupon($request->validated());

            return $this->success(
                new CouponResource($coupon),
                'Coupon created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->error('Failed to create coupon', $e->getMessage(), 500);
        }
    }

    /**
     * Get coupon details
     * GET /api/coupons/{coupon}
     */
    public function show(Request $request, Coupon $coupon)
    {
        try {
            $this->authorize('view', $coupon);

            $coupon->load(['allowedUsers', 'users']);

            return $this->success(
                new CouponResource($coupon),
                'Coupon retrieved successfully'
            );
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbidden('Unauthorized access');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve coupon', $e->getMessage(), 500);
        }
    }

    /**
     * Update coupon (Admin)
     * PUT /api/coupons/{coupon}
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        try {
            $updatedCoupon = $this->couponService->updateCoupon($coupon, $request->validated());

            return $this->success(
                new CouponResource($updatedCoupon),
                'Coupon updated successfully'
            );
        } catch (\Exception $e) {
            return $this->error('Failed to update coupon', $e->getMessage(), 500);
        }
    }

    /**
     * Delete coupon (Admin)
     * DELETE /api/coupons/{coupon}
     */
    public function destroy(Request $request, Coupon $coupon)
    {
        try {
            $this->authorize('delete', $coupon);

            $this->couponService->deleteCoupon($coupon);

            return $this->success(null, 'Coupon deleted successfully');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbidden('Unauthorized access');
        } catch (\Exception $e) {
            return $this->error('Failed to delete coupon', $e->getMessage(), 500);
        }
    }

    /**
     * Validate coupon code
     * POST /api/coupons/validate
     */
    public function validate(ValidateCouponRequest $request)
    {
        try {
            $user = $request->user();

            $result = $this->couponService->validateCoupon(
                $request->code,
                $user,
                $request->order_amount
            );

            if ($result['valid']) {
                return $this->success([
                    'valid' => true,
                    'message' => $result['message'],
                    'coupon' => new CouponResource($result['coupon']),
                    'discount' => (float) $result['discount'],
                ], 'Coupon is valid');
            } else {
                return $this->error($result['message'], null, 422);
            }
        } catch (\Exception $e) {
            return $this->error('Failed to validate coupon', $e->getMessage(), 500);
        }
    }

    /**
     * Toggle coupon status (Admin)
     * PATCH /api/coupons/{coupon}/toggle
     */
    public function toggleStatus(Request $request, Coupon $coupon)
    {
        try {
            $this->authorize('update', $coupon);

            $updatedCoupon = $this->couponService->toggleStatus($coupon);

            return $this->success(
                new CouponResource($updatedCoupon),
                'Coupon status updated successfully'
            );
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbidden('Unauthorized access');
        } catch (\Exception $e) {
            return $this->error('Failed to update status', $e->getMessage(), 500);
        }
    }

    /**
     * Get coupon statistics (Admin)
     * GET /api/coupons/{coupon}/stats
     */
    public function stats(Request $request, Coupon $coupon)
    {
        try {
            $this->authorize('view', $coupon);

            $stats = $this->couponService->getCouponStats($coupon);

            return $this->success($stats, 'Coupon statistics retrieved successfully');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbidden('Unauthorized access');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve statistics', $e->getMessage(), 500);
        }
    }
}
