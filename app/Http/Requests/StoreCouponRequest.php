<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('manage coupons');
    }

    public function rules(): array
    {
        return [
            'code' => 'nullable|string|max:50|unique:coupons,code',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:percentage,fixed',
            'value' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    if ($this->type === 'percentage' && $value > 100) {
                        $fail('Percentage value cannot exceed 100%.');
                    }
                },
            ],
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'required|integer|min:1',
            'starts_at' => 'nullable|date|before:expires_at',
            'expires_at' => 'required|date|after:now',
            'min_order_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'allowed_user_ids' => 'nullable|array',
            'allowed_user_ids.*' => 'exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique' => 'This coupon code already exists.',
            'type.required' => 'Please select a coupon type.',
            'type.in' => 'Invalid coupon type.',
            'value.required' => 'Please enter a discount value.',
            'value.min' => 'Discount value must be greater than 0.',
            'max_uses_per_user.required' => 'Please specify maximum uses per user.',
            'max_uses_per_user.min' => 'Each user must be able to use the coupon at least once.',
            'expires_at.required' => 'Please set an expiration date.',
            'expires_at.after' => 'Expiration date must be in the future.',
            'starts_at.before' => 'Start date must be before expiration date.',
            'allowed_user_ids.*.exists' => 'One or more selected users do not exist.',
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
