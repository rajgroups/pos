<?php

namespace App\Services;

use App\Repositories\VehicleTypeRepository;
use Illuminate\Support\Collection;

class VehicleTypeService
{
    public function __construct(protected VehicleTypeRepository $vehicleTypeRepository)
    {
    }

    public function getVehicleTypesWithSubCategories(bool $activeOnly = true): Collection
    {
        return $this->vehicleTypeRepository
            ->getMainCategoriesWithSubCategories($activeOnly)
            ->map(function ($vehicleType) {
                return [
                    'id' => $vehicleType->id,
                    'type_key' => $vehicleType->type_key,
                    'label' => $vehicleType->name,
                    'slug' => $vehicleType->slug,
                    'icon' => $vehicleType->icon,
                    'accent_color' => $vehicleType->accent_color,
                    'sheet_gradient' => array_values(array_filter([
                        $vehicleType->gradient_start,
                        $vehicleType->gradient_end,
                    ])),
                    'tagline' => $vehicleType->tagline,
                    'starting_fare' => $vehicleType->starting_fare,
                    'description' => $vehicleType->description,
                    'sub_categories' => $vehicleType->subCategories->map(function ($subCategory) {
                        return [
                            'id' => $subCategory->id,
                            'name' => $subCategory->name,
                            'slug' => $subCategory->slug,
                            'price' => $subCategory->price_label,
                            'description' => $subCategory->description,
                            'eta' => $subCategory->eta,
                            'seats' => $subCategory->max_capacity,
                        ];
                    })->values(),
                ];
            })
            ->values();
    }
}
