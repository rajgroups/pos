<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleCategoryPricing extends Model
{
    use HasFactory;

    protected $table = 'vehicle_category_pricings';

    protected $fillable = [
        'vehicle_category_id',
        'pricing_type',
        'base_fare',
        'minimum_fare',
        'per_km_rate',
        'per_hour_rate',
        'per_day_rate',
        'per_acre_rate',
        'per_ton_rate',
        'waiting_charge_per_hour',
        'night_charge_percentage',
        'surge_multiplier',
        'is_active',
    ];

    protected $casts = [
        'base_fare' => 'decimal:2',
        'minimum_fare' => 'decimal:2',
        'per_km_rate' => 'decimal:2',
        'per_hour_rate' => 'decimal:2',
        'per_day_rate' => 'decimal:2',
        'per_acre_rate' => 'decimal:2',
        'per_ton_rate' => 'decimal:2',
        'waiting_charge_per_hour' => 'decimal:2',
        'night_charge_percentage' => 'decimal:2',
        'surge_multiplier' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function vehicleCategory(): BelongsTo
    {
        return $this->belongsTo(VehicleCategory::class, 'vehicle_category_id');
    }
}
