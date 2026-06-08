<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleCategoryPricingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vehicle_category_id' => $this->vehicle_category_id,
            'pricing_type' => $this->pricing_type,
            'base_fare' => $this->base_fare,
            'minimum_fare' => $this->minimum_fare,
            'per_km_rate' => $this->per_km_rate,
            'per_hour_rate' => $this->per_hour_rate,
            'per_day_rate' => $this->per_day_rate,
            'per_acre_rate' => $this->per_acre_rate,
            'per_ton_rate' => $this->per_ton_rate,
            'waiting_charge_per_hour' => $this->waiting_charge_per_hour,
            'night_charge_percentage' => $this->night_charge_percentage,
            'surge_multiplier' => $this->surge_multiplier,
            'is_active' => $this->is_active,
        ];
    }
}
