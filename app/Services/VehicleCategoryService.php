<?php

namespace App\Services;

use App\Models\VehicleCategory;
use App\Repositories\VehicleCategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class VehicleCategoryService
{
    public function __construct(protected VehicleCategoryRepository $vehicleCategoryRepository)
    {
    }

    public function getCategories(bool $activeOnly = true): Collection
    {
        return $this->vehicleCategoryRepository->getMainCategories($activeOnly);
    }

    public function getCategoryWithPricing(int|string $categoryId): ?VehicleCategory
    {
        return $this->vehicleCategoryRepository->findWithRelations($categoryId);
    }
}
