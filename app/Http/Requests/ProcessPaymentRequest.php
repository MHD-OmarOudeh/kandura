<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_intent_id' => 'nullable|string', // For Stripe confirmation
            'payment_method_id' => 'nullable|string', // For Stripe new payment
        ];
    }
}
