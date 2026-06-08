<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class BookingCompleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'final_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:30'],
            'payment_status' => ['nullable', 'string', 'max:30'],
            'usage' => ['nullable', 'array'],
            'usage.distance_km' => ['nullable', 'numeric', 'min:0'],
            'usage.hours_used' => ['nullable', 'numeric', 'min:0'],
            'usage.acre_used' => ['nullable', 'numeric', 'min:0'],
            'usage.weight_ton' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
