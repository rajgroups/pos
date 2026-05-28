<?php

namespace App\Services;

use App\Repositories\VehicleRepository;
use Illuminate\Support\Collection;

class VehicleService
{
    public function __construct(protected VehicleRepository $vehicleRepository)
    {
    }

    public function getVehiclesByCategory(int $vehicleCategoryId, ?string $status = null, bool $verifiedOnly = false): Collection
    {
        return $this->vehicleRepository
            ->getByCategory($vehicleCategoryId, $status, $verifiedOnly)
            ->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'vehicle_category_id' => $vehicle->vehicle_category_id,
                    'vehicle_category' => $vehicle->vehicleType?->name,
                    'vehicle_number' => $vehicle->vehicle_number,
                    'brand' => $vehicle->brand,
                    'model' => $vehicle->model,
                    'color' => $vehicle->color,
                    'manufacture_year' => $vehicle->manufacture_year,
                    'seating_capacity' => $vehicle->seating_capacity,
                    'load_capacity' => $vehicle->load_capacity,
                    'status' => $vehicle->status,
                    'is_verified' => $vehicle->is_verified,
                    'images' => array_filter([
                        'front' => $vehicle->front_image,
                        'back' => $vehicle->back_image,
                        'side' => $vehicle->side_image,
                    ]),
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
