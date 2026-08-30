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

    public function messages(): array
    {
        return [
            'vehicle_category_id.required' => 'Vehicle category is required.',
            'vehicle_category_id.exists' => 'Selected vehicle category not found.',

            'booking_mode.in' => 'Booking mode must be instant or scheduled.',

            'scheduled_at.required_if' => 'Scheduled date and time is required for scheduled bookings.',

            'driver_id.exists' => 'Selected driver not found.',
            'vehicle_id.exists' => 'Selected vehicle not found.',

            'locations.required' => 'At least one location is required.',
            'locations.array' => 'Locations must be an array.',

            'locations.*.location_type.required' => 'Location type is required.',
            'locations.*.latitude.required' => 'Latitude is required.',
            'locations.*.longitude.required' => 'Longitude is required.',

            'locations.*.latitude.between' => 'Latitude must be between -90 and 90.',
            'locations.*.longitude.between' => 'Longitude must be between -180 and 180.',

            'usage.distance_km.numeric' => 'Distance must be numeric.',
            'usage.hours_used.numeric' => 'Hours used must be numeric.',
            'usage.acre_used.numeric' => 'Acre used must be numeric.',
            'usage.weight_ton.numeric' => 'Weight must be numeric.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('vehicle_category_id')) {
                $category = \App\Models\VehicleCategory::find($this->input('vehicle_category_id'));
                if ($category && (bool) $category->drop_location_required) {
                    $locations = $this->input('locations', []);
                    $hasDrop = collect($locations)->contains(fn ($l) => ($l['location_type'] ?? '') === 'drop');
                    if (! $hasDrop) {
                        $validator->errors()->add('locations', 'Drop location is required for this vehicle category.');
                    }
                }
            }
        });
    }
}
