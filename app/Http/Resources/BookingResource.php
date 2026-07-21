<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_no' => $this->booking_no,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'driver_id' => $this->driver_id,
            'vehicle_id' => $this->vehicle_id,
            'vehicle_category_id' => $this->vehicle_category_id,
            'category_name' => $this->category_name,
            'booking_mode' => $this->service_mode,
            'service_mode' => $this->service_mode,
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'duration_hours' => $this->duration_hours,
            'pickup_address' => $this->pickup_address,
            'drop_address' => $this->drop_address,
            'requires_drop_location' => in_array($this->category?->type_key, ['cab', 'auto', 'bike', 'truck', 'parcel'], true),
            'start_otp' => $this->when($request->boolean('include_otp', false), $this->start_otp),
            'estimated_amount' => $this->estimated_amount,
            'final_amount' => $this->final_amount,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'otp_verified_at' => $this->otp_verified_at?->toIso8601String(),
            'accepted_at' => $this->accepted_at?->toIso8601String(),
            'arrived_at' => $this->arrived_at?->toIso8601String(),
            'started_at' => $this->started_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
            'category' => new VehicleCategoryResource($this->whenLoaded('category')),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user?->id,
                    'name' => $this->user?->name,
                    'email' => $this->user?->email,
                    'phone' => $this->user?->phone,
                ];
            }),
            'driver' => $this->whenLoaded('driver', function () {
                return [
                    'id' => $this->driver?->id,
                    'name' => $this->driver?->name,
                    'phone' => $this->driver?->phone,
                    'driver_type' => $this->driver?->driver_type,
                    'status' => $this->driver?->status,
                    'latitude' => $this->vehicle?->location?->latitude,
                    'longitude' => $this->vehicle?->location?->longitude,
                ];
            }),
            'vehicle' => $this->whenLoaded('vehicle', function () {
                return [
                    'id' => $this->vehicle?->id,
                    'vehicle_number' => $this->vehicle?->vehicle_number,
                    'brand' => $this->vehicle?->brand,
                    'model' => $this->vehicle?->model,
                    'status' => $this->vehicle?->status,
                ];
            }),
            'locations' => $this->relationLoaded('locations')
                ? BookingLocationResource::collection($this->locations)
                : [],
            'pickup_location' => $this->relationLoaded('pickupLocation')
                ? new BookingLocationResource($this->pickupLocation)
                : null,
            'drop_location' => $this->relationLoaded('dropLocation')
                ? new BookingLocationResource($this->dropLocation)
                : null,
            'usage' => $this->relationLoaded('usage')
                ? new BookingUsageResource($this->usage)
                : null,
            'fare' => $this->relationLoaded('fare')
                ? new BookingFareResource($this->fare)
                : null,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
