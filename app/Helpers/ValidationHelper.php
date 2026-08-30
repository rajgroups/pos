<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Unique;

class ValidationHelper
{
    /**
     * GENERIC VALIDATION
     * Returns:
     *  - status: success/error
     *  - message: first error
     *  - errors: all errors (optional)
     */
    public static function validate(array $data, array $rules, array $messages = [])
    {
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return [
                KeywordHelper::STATUS  => KeywordHelper::ERROR,
                KeywordHelper::MESSAGE => $validator->errors()->first(),
                KeywordHelper::ERRORS  => $validator->errors()->messages(),
            ];
        }

        return [
            KeywordHelper::STATUS => KeywordHelper::SUCCESS,
            KeywordHelper::DATA   => $validator->validated(),
        ];
    }


    /**
     * CATEGORY CREATE/UPDATE VALIDATION
     */
    public static function validateCategory(array $data, $isUpdate = false, $id = null)
    {
        $rules = [
            'name'      => 'required|unique:category,name' . ($isUpdate ? ',' . $id : ''),
            'slug'      => 'required|unique:category,slug' . ($isUpdate ? ',' . $id : ''),
            'status'    => 'required|in:0,1',
            'parent_id' => 'nullable',

            'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'icon'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];

        $messages = [
            // Name
            'name.required' => __('string.category.name_required'),
            'name.unique'   => __('string.category.name_unique'),

            // Slug
            'slug.required' => __('string.category.slug_required'),
            'slug.unique'   => __('string.category.slug_unique'),

            // Status
            'status.required' => __('string.category.status_required'),

            // Image messages
            'image.image'  => __('string.category.image_invalid'),
            'image.mimes'  => __('string.category.image_extension'),
            'image.max'    => __('string.category.max_file_size'),

            // Icon messages
            'icon.image'  => __('string.category.image_invalid'),
            'icon.mimes'  => __('string.category.image_extension'),
            'icon.max'    => __('string.category.max_file_size'),
        ];

        return self::validate($data, $rules, $messages);
    }



    /**
     * CHECK CATEGORY EXIST
     */
    public static function validateCategoryExist($id)
    {
        $rules = [
            'id' => 'required|exists:category,id',
        ];

        $messages = [
            'id.required' => __('string.category.id_required'),
            'id.exists'   => __('string.category.id_not_found'),
        ];

        return self::validate(['id' => $id], $rules, $messages);
    }

    /*
    * Brand Validation
    */

    public static function validateBrand(array $data, $isUpdate = false, $id = null){

        $rules = [
            'name'      => 'required|unique:brand,name'.($isUpdate ? ',' .$id : ''),
            'slug'      => 'required|unique:brand,slug'.($isUpdate ? ','.$id : ''),

            'status'    => 'required|in:0,1',

            'image'     => ($isUpdate ? 'nullable' : 'required') . '|image|mimes:jpg,jpeg,png,webp|max:5120',
            'icon'      => ($isUpdate ? 'nullable' : 'required') .'|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];

        $messages = [
            // Name
            'name.required' => __('string.brand.name_required'),
            'name.unique'   => __('string.brand.name_unique'),

            // Slug
            'slug.required' => __('string.brand.slug_required'),
            'slug.unique'   => __('string.brand.slug_unique'),

            // Status
            'status.required' => __('string.brand.status_required'),

            // Image
            'image.required' => __('string.brand.image_required'),
            'image.image'    => __('string.brand.image_invalid'),
            'image.mimes'    => __('string.brand.image_extension'),
            'image.max'      => __('string.brand.image_max'),

            // Icon
            'icon.required' => __('string.brand.icon_required'),
            'icon.image'    => __('string.brand.icon_invalid'),
            'icon.mimes'    => __('string.brand.icon_extension'),
            'icon.max'      => __('string.brand.icon_max'),
        ];

        return self::validate($data, $rules ,$messages);
    }

    /**
     * CHECK CATEGORY EXIST
     */
    public static function validateBrandExist($id)
    {
        $rules = [
            'id' => 'required|exists:brand,id',
        ];

        $messages = [
            'id.required' => __('string.brand.id_required'),
            'id.exists'   => __('string.category.id_not_found'),
        ];

        return self::validate(['id' => $id], $rules, $messages);
    }

    /**
     * Check Mobile number Validation
     **/

    public static function ValidateMobile($mobile)
    {
        $rules = [
            'mobile' => [
                'required',
                'regex:/^(\+91|91)?[6-9][0-9]{9}$/'
            ]
        ];

        $message = [
            'mobile.required' => 'Please enter the mobile number',
            'mobile.regex'    => 'Please enter valid mobile number',
        ];

        return self::validate(
            ['mobile' => $mobile],
            $rules,
            $message
        );
    }

    /**
     * Validate OTP Request
     *
     * Validate mobile number and OTP.
     *
     * @param object $request
     *
     * @return array
     */
    public static function ValidateOtp($request)
    {
        $rules = [
            'mobile' => [
                'required',
                'regex:/^(\+91|91)?[6-9][0-9]{9}$/'
            ],
            'otp' => [
                'required',
                'digits:4'
            ]
        ];

        $message = [
            'mobile.required' => 'Please enter mobile number',
            'mobile.regex'    => 'Please enter valid mobile number',

            'otp.required'    => 'Please enter OTP',
            'otp.digits'      => 'OTP must be 4 digits',
        ];

        return self::validate(
            [
                'mobile' => $request->mobile,
                'otp'    => $request->otp,
            ],
            $rules,
            $message
        );
    }

    /**
     * Validate vehicle listing filters.
     */
    public static function validateVehicleIndex(array $data)
    {
        if (
            !array_key_exists('vehicle_category_id', $data)
            && array_key_exists('category', $data)
        ) {
            $data['vehicle_category_id'] = $data['category'];
        }

        $rules = [
            'vehicle_category_id' => 'nullable|integer|exists:vehicle_categories,id',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0',
            'type' => 'nullable|string|max:100',
            'search' => 'nullable|string|max:100',
            'status' => 'nullable|in:active,inactive,maintenance,retired',
            'verified_only' => 'nullable|boolean',
        ];

        $messages = [
            'vehicle_category_id.integer' => 'Vehicle category id must be a valid integer.',
            'vehicle_category_id.exists' => 'Selected vehicle category was not found.',
            'lat.numeric' => 'Latitude must be a valid number.',
            'lat.between' => 'Latitude must be between -90 and 90.',
            'lng.numeric' => 'Longitude must be a valid number.',
            'lng.between' => 'Longitude must be between -180 and 180.',
            'radius.numeric' => 'Radius must be a valid number.',
            'radius.min' => 'Radius must be zero or greater.',
            'type.string' => 'Type must be a valid string.',
            'search.string' => 'Search must be a valid string.',
            'status.in' => 'Status must be active, inactive, maintenance, or retired.',
            'verified_only.boolean' => 'Verified only must be true or false.',
        ];

        return self::validate($data, $rules, $messages);
    }

    /**
     * Validate the request for storing a booking.
     *
     * @param array $data
     * @return array
     */
    public static function validateBookingStore(array $data)
    {
        $rules = [
            // Core booking details
            'vehicle_category_id' => 'required|exists:vehicle_categories,id',
            'booking_mode' => 'required|in:instant,scheduled',
            'payment_method' => 'required|in:cash,online,wallet',

            // Conditional fields for different booking modes
            // 'driver_id' => 'nullable|required_if:booking_mode,instant|exists:drivers,id',
            // 'vehicle_id' => 'nullable|required_if:booking_mode,instant|exists:vehicles,id',
            'scheduled_at' => 'nullable|required_if:booking_mode,scheduled|date_format:Y-m-d H:i:s|after_or_equal:now',
            'duration_hours' => 'nullable|numeric|min:1',

            // Locations array validation
            'locations' => 'required|array|min:1',
            'locations.*.location_type' => 'required|string|max:50',
            'locations.*.latitude' => 'required|numeric|between:-90,90',
            'locations.*.longitude' => 'required|numeric|between:-180,180',
            'locations.*.address' => 'required|string|max:255',
            'locations.*.sequence' => 'required|integer|min:1',

            // Usage object validation (fields are optional within the object)
            'usage' => 'nullable|array',
            'usage.distance_km' => 'nullable|numeric|min:0',
            'usage.hours_used' => 'nullable|numeric|min:0',
            'usage.weight_ton' => 'nullable|numeric|min:0',
            'usage.acre_used' => 'nullable|numeric|min:0',
        ];

        $messages = [
            'vehicle_category_id.required' => 'The vehicle category is required.',
            'vehicle_category_id.exists' => 'The selected vehicle category does not exist.',
            'booking_mode.required' => 'The booking mode (instant or scheduled) is required.',
            'scheduled_at.required_if' => 'The scheduled date and time is required for scheduled bookings.',
            'scheduled_at.after_or_equal' => 'The scheduled time must be in the future.',
            'locations.required' => 'At least one location (pickup) is required.',
            'locations.*.address.required' => 'An address is required for all locations.',
        ];

        $validation = self::validate($data, $rules, $messages);
        if ($validation[KeywordHelper::STATUS] === KeywordHelper::ERROR) {
            return $validation;
        }

        if (! empty($data['vehicle_category_id'])) {
            $category = \App\Models\VehicleCategory::find($data['vehicle_category_id']);
            if ($category && (bool) $category->drop_location_required) {
                $locations = $data['locations'] ?? [];
                $hasDropLocation = collect($locations)->contains(function ($location) {
                    return ($location['location_type'] ?? '') === 'drop';
                });

                if (! $hasDropLocation) {
                    return [
                        KeywordHelper::STATUS  => KeywordHelper::ERROR,
                        KeywordHelper::MESSAGE => 'Drop location is required for this vehicle category.',
                        KeywordHelper::ERRORS  => [
                            'locations' => ['Drop location is required for this vehicle category.'],
                        ],
                    ];
                }
            }
        }

        return $validation;
    }

    public static function ValidateAcceptBooking(array $data)
    {
        $rules = [
            'driver_id' => [
                'required',
                'integer',
                'exists:drivers,id',
            ],
            'vehicle_id' => [
                'nullable',
                'integer',
                'exists:vehicles,id',
            ],
        ];

        $messages = [
            'driver_id.required' => 'Driver ID is required.',
            'driver_id.integer'  => 'Driver ID must be an integer.',
            'driver_id.exists'   => 'Driver not found.',

            'vehicle_id.required' => 'Vehicle ID is required.',
            'vehicle_id.integer'  => 'Vehicle ID must be an integer.',
            'vehicle_id.exists'   => 'Vehicle not found.',
        ];

        return self::validate($data, $rules, $messages);
    }
}
