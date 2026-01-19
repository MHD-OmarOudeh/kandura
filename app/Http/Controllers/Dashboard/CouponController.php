<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Http\Services\CouponService;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    protected CouponService $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    /**
     * Display listing of coupons
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'type',
            'is_active',
            'status',
            'per_page'
        ]);

        $coupons = $this->couponService->getAllCoupons($filters);

        return view('dashboard.coupons.index', compact('coupons'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();

        return view('dashboard.coupons.create', compact('users'));
    }

    /**
     * Store new coupon
     */
    public function store(StoreCouponRequest $request)
    {
        try {
            $this->couponService->createCoupon($request->validated());

            return redirect()
                ->route('dashboard.coupons.index')
                ->with('success', 'Coupon created successfully!');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to create coupon: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show edit form
     */
    public function edit(Coupon $coupon)
    {
        $coupon->load('allowedUsers');
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();

        return view('dashboard.coupons.edit', compact('coupon', 'users'));
    }

    /**
     * Update coupon
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        try {
            $this->couponService->updateCoupon($coupon, $request->validated());

            return redirect()
                ->route('dashboard.coupons.index')
                ->with('success', 'Coupon updated successfully!');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to update: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Toggle coupon status
     */
    public function toggleStatus(Coupon $coupon)
    {
        try {
            $this->couponService->toggleStatus($coupon);

            return redirect()
                ->route('dashboard.coupons.index')
                ->with('success', 'Coupon status updated!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update status']);
        }
    }

    /**
     * Delete coupon
     */
    public function destroy(Coupon $coupon)
    {
        try {
            $this->couponService->deleteCoupon($coupon);

            return redirect()
                ->route('dashboard.coupons.index')
                ->with('success', 'Coupon deleted successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
