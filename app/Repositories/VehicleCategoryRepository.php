<?php

namespace App\Repositories;

use App\Models\VehicleCategory;
use Illuminate\Database\Eloquent\Collection;

class VehicleCategoryRepository
{
    public function getMainCategories(bool $activeOnly = true): Collection
    {
        return VehicleCategory::query()
            ->when($activeOnly, fn ($query) => $query->where('is_active', true))
            ->whereNull('parent_id')
            ->with([
                'pricing',
                'children' => fn ($query) => $query
                    ->with('pricing')
                    ->orderBy('sort_order')
                    ->orderBy('id'),
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function findWithRelations(int|string $id): ?VehicleCategory
    {
        return VehicleCategory::query()
            ->with(['pricing', 'children.pricing', 'vehicles'])
            ->find($id);
    }
}
