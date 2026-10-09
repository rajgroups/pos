<?php

namespace App\Services;

use App\Repositories\VehicleTypeRepository;
use Illuminate\Support\Collection;

class VehicleTypeService
{
    public function __construct(
        protected VehicleTypeRepository $vehicleTypeRepository,
        protected FareCalculationService $fareCalculationService
    ) {}

    public function getVehicleTypesWithSubCategories(bool $activeOnly = true, ?float $distanceKm = null): Collection
    {
        return $this->vehicleTypeRepository
            ->getMainCategoriesWithSubCategories($activeOnly)
            ->map(function ($vehicleType) use ($distanceKm) {
                return [
                    'id' => $vehicleType->id,
                    'type_key' => $vehicleType->type_key,
                    'label' => $vehicleType->name,
                    'slug' => $vehicleType->slug,
                    'icon' => $vehicleType->icon,
                    'icon_url' => $vehicleType->icon && str_contains($vehicleType->icon, '/')
                        ? (str_starts_with($vehicleType->icon, 'upload/') ? asset(ltrim($vehicleType->icon, '/')) : asset('storage/' . ltrim($vehicleType->icon, '/')))
                        : null,
                    'image' => $vehicleType->image,
                    'image_url' => $vehicleType->image
                        ? (str_starts_with($vehicleType->image, 'upload/') ? asset(ltrim($vehicleType->image, '/')) : asset('storage/' . ltrim($vehicleType->image, '/')))
                        : null,
                    'accent_color' => $vehicleType->accent_color,
                    'sheet_gradient' => array_values(array_filter([
                        $vehicleType->gradient_start,
                        $vehicleType->gradient_end,
                    ])),
                    'tagline' => $vehicleType->tagline,
                    'starting_fare' => $vehicleType->starting_fare,
                    'description' => $vehicleType->description,
                    // Strictly 0 or 1 — NOT NULL DEFAULT 1.
                    'drop_location_required' => (bool) $vehicleType->drop_location_required,
                    'sub_categories' => $vehicleType->subCategories->map(function ($subCategory) use ($distanceKm) {
                        $calculatedFare = null;
                        
                        if ($distanceKm !== null && $distanceKm > 0 && $subCategory->pricing) {
                            $fareBreakdown = $this->fareCalculationService->calculate(
                                $subCategory,
                                ['distance_km' => $distanceKm]
                            );
                            if (isset($fareBreakdown['user_total'])) {
                                $calculatedFare = '₹' . number_format($fareBreakdown['user_total'], 0);
                            }
                        }

                        return [
                            'id' => $subCategory->id,
                            'name' => $subCategory->name,
                            'slug' => $subCategory->slug,
                            'icon' => $subCategory->icon,
                            'icon_url' => $subCategory->icon && str_contains($subCategory->icon, '/')
                                ? (str_starts_with($subCategory->icon, 'upload/') ? asset(ltrim($subCategory->icon, '/')) : asset('storage/' . ltrim($subCategory->icon, '/')))
                                : null,
                            'image' => $subCategory->image,
                            'image_url' => $subCategory->image
                                ? (str_starts_with($subCategory->image, 'upload/') ? asset(ltrim($subCategory->image, '/')) : asset('storage/' . ltrim($subCategory->image, '/')))
                                : null,
                            'price' => $subCategory->price_label,
                            'description' => $subCategory->description,
                            'eta' => $subCategory->eta,
                            'seats' => $subCategory->max_capacity,
                            'calculated_fare' => $calculatedFare,
                            // Strictly 0 or 1 — NOT NULL DEFAULT 1.
                            'drop_location_required' => (bool) $subCategory->drop_location_required,
                        ];
                    })->values(),
                ];
            })
            ->values();
    }
}
