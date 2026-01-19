<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|exists:coupons,code',
            'order_amount' => 'required|numeric|min:0.01',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Please enter a coupon code.',
            'code.exists' => 'Invalid coupon code.',
            'order_amount.required' => 'Order amount is required.',
            'order_amount.min' => 'Invalid order amount.',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper($this->code),
            ]);
        }
    }
}
