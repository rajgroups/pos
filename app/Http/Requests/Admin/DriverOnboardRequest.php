<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DriverOnboardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $driverId = $this->route('driver') ? $this->route('driver')->id : ($this->route('id') ?? null);
        $vehicleId = $this->input('vehicle_id'); // If updating an existing vehicle

        return [
            // Driver Rules
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:20|unique:drivers,phone,' . $driverId,
            'email'    => 'nullable|email|unique:drivers,email,' . $driverId,
            'dob'      => 'nullable|date',
            'gender'   => 'nullable|in:male,female,other',
            'driver_type' => 'nullable|string',
            'address'  => 'nullable|string',
            'city'     => 'nullable|string|max:255',
            'state'    => 'nullable|string|max:255',
            'pincode'  => 'nullable|string|max:20',
            
            // Driver Documents/IDs
            'aadhaar_number' => 'nullable|string|max:20',
            'pan_number'     => 'nullable|string|max:20',
            'license_number' => 'required|string|max:255|unique:drivers,license_number,' . $driverId,
            'license_expiry' => 'required|date',
            'license_categories' => 'nullable|array',
            'license_categories.*' => 'string',
            'status'      => 'nullable|in:active,inactive,blocked',
            'remarks'     => 'nullable|string',

            // Files (Driver)
            'profile_photo' => 'nullable|image|max:5120',
            'license_front' => 'nullable|image|max:5120',
            'license_back' => 'nullable|image|max:5120',
            
            // Vehicle Rules
            'register_vehicle' => 'nullable|boolean', // Checkbox to register a new vehicle
            'vehicle_category_id' => 'required_if:register_vehicle,1|nullable|exists:vehicle_categories,id',
            'vehicle_number' => 'required_if:register_vehicle,1|nullable|string|max:255|unique:vehicles,vehicle_number,' . $vehicleId,
            'brand'          => 'nullable|string|max:255',
            'model'          => 'nullable|string|max:255',
            'color'          => 'nullable|string|max:255',
            'manufacture_year' => 'nullable|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'rc_number'      => 'nullable|string|max:255',
            'seating_capacity' => 'nullable|integer|min:1',
            
            // Vehicle Files
            'front_image' => 'nullable|image|max:5120',
            'back_image'  => 'nullable|image|max:5120',
        ];
    }
    
    public function messages(): array
    {
        return [
            'vehicle_category_id.required_if' => 'Please select a vehicle category when registering a vehicle.',
            'vehicle_number.required_if' => 'The vehicle number is required when registering a vehicle.',
        ];
    }
}
