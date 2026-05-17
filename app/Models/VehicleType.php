<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleType extends Model
{
    use HasFactory;

    protected $table = 'vehicle_categories';

    protected $fillable = [
        'parent_id',
        'type_key',
        'name',
        'slug',
        'icon',
        'description',
        'tagline',
        'image',
        'accent_color',
        'gradient_start',
        'gradient_end',
        'base_fare',
        'per_km_rate',
        'price_label',
        'starting_fare',
        'eta',
        'max_capacity',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'base_fare' => 'decimal:2',
        'per_km_rate' => 'decimal:2',
        'max_capacity' => 'integer',
        'sort_order' => 'integer',
    ];

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'vehicle_category_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function subCategories(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function documentMaps(): HasMany
    {
        return $this->hasMany(VehicleTypeDocumentMap::class, 'vehicle_type_id');
    }
}
