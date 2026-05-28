<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'driver_id',
        'vehicle_category_id',
        'brand',
        'model',
        'vehicle_number',
        'color',
        'manufacture_year',
        'rc_number',
        'rc_expiry',
        'insurance_number',
        'insurance_expiry',
        'permit_number',
        'permit_expiry',
        'fitness_certificate_number',
        'fitness_expiry',
        'seating_capacity',
        'load_capacity',
        'front_image',
        'back_image',
        'side_image',
        'status',
        'is_verified',
    ];

    protected $casts = [
        'manufacture_year' => 'integer',
        'rc_expiry' => 'date',
        'insurance_expiry' => 'date',
        'permit_expiry' => 'date',
        'fitness_expiry' => 'date',
        'seating_capacity' => 'integer',
        'load_capacity' => 'decimal:2',
        'is_verified' => 'boolean',
    ];

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_category_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VehicleDocument::class);
    }

    public function driverAssignments(): HasMany
    {
        return $this->hasMany(DriverVehicleAssignment::class);
    }

    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(Driver::class, 'driver_vehicle_assignments')
            ->withPivot(['assigned_from', 'assigned_to', 'is_current'])
            ->withTimestamps();
    }
}
