<?php

namespace App\Http\Requests;

use App\Models\Address;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'address_id' => [
                'required',
                'exists:addresses,id',
                function ($attribute, $value, $fail) {
                    $userId = Auth::id();
                    if (!Address::where('id', $value)->where('user_id', $userId)->exists()) {
                        $fail('The selected address does not belong to you.');

                    }
                },
            ],
            'payment_method' => 'required|in:cash,wallet,card',
            'coupon_code' => 'nullable|string|exists:coupons,code',
            'notes' => 'nullable|string|max:1000',

            // Order items
            'items' => 'required|array|min:1',
            'items.*.design_id' => 'required|exists:designs,id',
            'items.*.quantity' => 'required|integer|min:1|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'address_id.required' => 'Please select a delivery address.',
            'address_id.exists' => 'The selected address is invalid.',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Invalid payment method selected.',
            'items.required' => 'Please add at least one item to your order.',
            'items.min' => 'Your order must contain at least one item.',
            'items.*.design_id.required' => 'Each item must have a design.',
            'items.*.design_id.exists' => 'One or more selected designs are invalid.',
            'items.*.quantity.required' => 'Please specify quantity for each item.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.quantity.max' => 'Maximum quantity per item is 100.',
        ];
    }

    /**
     * Get custom attribute names
     */
    public function attributes(): array
    {
        return [
            'address_id' => 'delivery address',
            'payment_method' => 'payment method',
            'coupon_code' => 'coupon code',
            'items' => 'order items',
            'items.*.design_id' => 'design',
            'items.*.quantity' => 'quantity',
        ];
    }
}
