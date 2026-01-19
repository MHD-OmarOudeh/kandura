<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletDepositRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('manage wallet');
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1|max:100000',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Please select a user.',
            'user_id.exists' => 'Selected user does not exist.',
            'amount.required' => 'Please enter an amount.',
            'amount.min' => 'Amount must be at least 1 SAR.',
            'amount.max' => 'Amount cannot exceed 100,000 SAR.',
        ];
    }
}
