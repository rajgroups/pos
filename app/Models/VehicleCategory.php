<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VehicleCategory extends Model
{
    use HasFactory;

    protected $table = 'vehicle_categories';

    protected $fillable = [
        'parent_id',
        'type_key',
        'service_mode',
        'name',
        'slug',
        'description',
        'tagline',
        'starting_fare',
        'price_label',
        'eta',
        'image',
        'icon',
        'accent_color',
        'gradient_start',
        'gradient_end',
        'max_capacity',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'service_mode' => 'string',
        'is_active' => 'boolean',
        'max_capacity' => 'integer',
        'sort_order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function subCategories(): HasMany
    {
        return $this->children()->orderBy('sort_order');
    }

    public function pricing(): HasOne
    {
        return $this->hasOne(VehicleCategoryPricing::class, 'vehicle_category_id');
    }

    public function activePricing(): HasOne
    {
        return $this->hasOne(VehicleCategoryPricing::class, 'vehicle_category_id')->where('is_active', true);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'vehicle_category_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'vehicle_category_id');
    }

    public function documentMaps(): HasMany
    {
        return $this->hasMany(VehicleTypeDocumentMap::class, 'vehicle_type_id');
    }
}
