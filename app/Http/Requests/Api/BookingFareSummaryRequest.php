<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class BookingFareSummaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_category_id' => ['required', 'integer', 'exists:vehicle_categories,id'],
            'usage' => ['nullable', 'array'],
            'usage.distance_km' => ['nullable', 'numeric', 'min:0'],
            'usage.hours_used' => ['nullable', 'numeric', 'min:0'],
            'usage.acre_used' => ['nullable', 'numeric', 'min:0'],
            'usage.weight_ton' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
