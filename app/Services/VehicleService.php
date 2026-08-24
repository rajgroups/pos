<?php

namespace App\Services;

use App\Repositories\VehicleRepository;
use Illuminate\Support\Collection;

class VehicleService
{
    public function __construct(protected VehicleRepository $vehicleRepository)
    {
    }

    public function getVehicles(array $filters = []): Collection
    {
        return $this->vehicleRepository
            ->getByFilters($filters)
            ->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'vehicle_category_id' => $vehicle->vehicle_category_id,
                    'vehicle_category' => $vehicle->vehicleType?->name,
                    'type' => $vehicle->vehicleType?->slug,
                    'vehicle_number' => $vehicle->vehicle_number,
                    'brand' => $vehicle->brand,
                    'model' => $vehicle->model,
                    'color' => $vehicle->color,
                    'manufacture_year' => $vehicle->manufacture_year,
                    'seating_capacity' => $vehicle->seating_capacity,
                    'load_capacity' => $vehicle->load_capacity,
                    'status' => $vehicle->status,
                    'is_verified' => $vehicle->is_verified,
                    'distance' => $vehicle->getAttribute('distance') !== null ? round((float) $vehicle->getAttribute('distance'), 2) : null,
                    'images' => array_filter([
                        'front' => $vehicle->front_image,
                        'back' => $vehicle->back_image,
                        'side' => $vehicle->side_image,
                    ]),
                    'category_icon' => $vehicle->vehicleType?->icon
                        ? asset('storage/' . ltrim($vehicle->vehicleType->icon, '/'))
                        : null,
                    'category_image' => $vehicle->vehicleType?->image
                        ? asset('storage/' . ltrim($vehicle->vehicleType->image, '/'))
                        : null,
                    'location' => $vehicle->location ? [
                        'latitude' => (float) $vehicle->location->latitude,
                        'longitude' => (float) $vehicle->location->longitude,
                        'speed' => $vehicle->location->speed !== null ? (float) $vehicle->location->speed : null,
                        'heading' => $vehicle->location->heading !== null ? (float) $vehicle->location->heading : null,
                        'accuracy' => $vehicle->location->accuracy !== null ? (float) $vehicle->location->accuracy : null,
                        'is_online' => $vehicle->location->is_online,
                        'updated_at' => $vehicle->location->location_updated_at?->toIso8601String(),
                    ] : null,
                    'driver' => $vehicle->driver ? [
                        'id' => $vehicle->driver->id,
                        'name' => $vehicle->driver->name,
                        'phone' => $vehicle->driver->phone,
                        'driver_type' => $vehicle->driver->driver_type,
                        'status' => $vehicle->driver->status,
                    ] : null,
                ];
            })
            ->values();
    }
}
