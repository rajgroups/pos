<?php

namespace App\Repositories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;

class VehicleRepository
{
    public function getByCategory(int $vehicleCategoryId, ?string $status = null, bool $verifiedOnly = false): Collection
    {
        return Vehicle::query()
            ->with(['vehicleType', 'driver'])
            ->where('vehicle_category_id', $vehicleCategoryId)
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($verifiedOnly, fn ($query) => $query->where('is_verified', true))
            ->orderBy('is_verified', 'desc')
            ->orderBy('id')
            ->get();
    }
}
