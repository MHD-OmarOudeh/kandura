<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('manage coupons');
    }

    public function rules(): array
    {
        $couponId = $this->route('coupon')->id;

        return [
            'code' => "nullable|string|max:50|unique:coupons,code,{$couponId}",
            'description' => 'nullable|string|max:1000',
            'type' => 'sometimes|in:percentage,fixed',
            'value' => [
                'sometimes',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    if ($this->type === 'percentage' && $value > 100) {
                        $fail('Percentage value cannot exceed 100%.');
                    }
                },
            ],
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'sometimes|integer|min:1',
            'starts_at' => 'nullable|date|before:expires_at',
            'expires_at' => 'sometimes|date',
            'min_order_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'allowed_user_ids' => 'nullable|array',
            'allowed_user_ids.*' => 'exists:users,id',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('code') && !empty($this->code)) {
            $this->merge([
                'code' => strtoupper($this->code),
            ]);
        }
    }
}
