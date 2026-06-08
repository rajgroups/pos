<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_category_id' => ['required', 'integer', 'exists:vehicle_categories,id'],
            'booking_mode' => ['nullable', 'string', Rule::in(['instant', 'scheduled'])],
            'scheduled_at' => ['nullable', 'required_if:booking_mode,scheduled', 'date'],
            'duration_hours' => ['nullable', 'numeric', 'min:0'],
            'driver_id' => ['nullable', 'integer', 'exists:drivers,id'],
            'vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
            'payment_method' => ['nullable', 'string', 'max:30'],
            'locations' => ['required', 'array', 'min:1'],
            'locations.*.location_type' => ['required', 'string', 'max:30'],
            'locations.*.latitude' => ['required', 'numeric', 'between:-90,90'],
            'locations.*.longitude' => ['required', 'numeric', 'between:-180,180'],
            'locations.*.address' => ['nullable', 'string', 'max:1000'],
            'locations.*.sequence' => ['nullable', 'integer', 'min:1'],
            'usage' => ['nullable', 'array'],
            'usage.distance_km' => ['nullable', 'numeric', 'min:0'],
            'usage.hours_used' => ['nullable', 'numeric', 'min:0'],
            'usage.acre_used' => ['nullable', 'numeric', 'min:0'],
            'usage.weight_ton' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
