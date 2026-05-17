<?php

namespace App\Repositories;

use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Collection;

class VehicleTypeRepository
{
    public function getMainCategoriesWithSubCategories(bool $activeOnly = true): Collection
    {
        return VehicleType::query()
            ->when($activeOnly, fn ($query) => $query->where('is_active', true))
            ->whereNull('parent_id')
            ->with([
                'subCategories' => fn ($query) => $query
                    ->when($activeOnly, fn ($subQuery) => $subQuery->where('is_active', true))
                    ->orderBy('sort_order')
                    ->orderBy('id'),
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
