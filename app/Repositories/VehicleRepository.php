<?php

namespace App\Repositories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class VehicleRepository
{
    public function getByFilters(array $filters = []): Collection
    {
        $query = Vehicle::query()
            ->with(['vehicleType', 'driver', 'location'])
            ->when(
                !empty($filters['vehicle_category_id']),
                fn (Builder $builder) => $builder->where('vehicle_category_id', (int) $filters['vehicle_category_id'])
            )
            ->when(
                !empty($filters['type']),
                fn (Builder $builder) => $builder->whereHas('vehicleType', function (Builder $vehicleTypeQuery) use ($filters) {
                    $type = (string) $filters['type'];

                    $vehicleTypeQuery
                        ->where('slug', $type)
                        ->orWhere('type_key', $type)
                        ->orWhere('name', 'like', "%{$type}%")
                        ->when(
                            ctype_digit($type),
                            fn (Builder $typedQuery) => $typedQuery->orWhere('id', (int) $type)
                        );
                })
            )
            ->when(
                !empty($filters['search']),
                fn (Builder $builder) => $builder->where(function (Builder $searchQuery) use ($filters) {
                    $search = trim((string) $filters['search']);

                    $searchQuery
                        ->where('vehicle_number', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhereHas('driver', fn (Builder $driverQuery) => $driverQuery->where('name', 'like', "%{$search}%"));
                })
            )
            ->when(!empty($filters['status']), fn (Builder $builder) => $builder->where('status', $filters['status']))
            ->when(!empty($filters['verified_only']), fn (Builder $builder) => $builder->where('is_verified', true))
            ->when(
                isset($filters['lat'], $filters['lng'], $filters['radius']),
                fn (Builder $builder) => $this->applyRadiusFilter(
                    $builder,
                    (float) $filters['lat'],
                    (float) $filters['lng'],
                    (float) $filters['radius']
                )
            )
            ->orderBy('is_verified', 'desc')
            ->orderBy('id');

        return $query->get();
    }

    protected function applyRadiusFilter(Builder $query, float $latitude, float $longitude, float $radius): Builder
    {
        $haversine = '(6371 * acos(cos(radians(?)) * cos(radians(vehicle_locations.latitude)) * cos(radians(vehicle_locations.longitude) - radians(?)) + sin(radians(?)) * sin(radians(vehicle_locations.latitude))))';

        return $query
            ->join('vehicle_locations', 'vehicle_locations.vehicle_id', '=', 'vehicles.id')
            ->select('vehicles.*')
            ->selectRaw("{$haversine} as distance", [$latitude, $longitude, $latitude])
            ->whereRaw("{$haversine} <= ?", [$latitude, $longitude, $latitude, $radius]);
    }
}
