<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Driver extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'otp',
        'email',
        'dob',
        'gender',
        'address',
        'city',
        'state',
        'pincode',
        'aadhaar_number',
        'pan_number',
        'license_number',
        'license_expiry',
        'license_categories',
        'driver_type',
        'status',
        'is_online',
        'fcm_token',
        'is_verified',
        'profile_photo',
        'license_front',
        'license_back',
        'aadhaar_front',
        'aadhaar_back',
        'pan_card_file',
        'police_verification_file',
        'medical_certificate',
        'remarks',
    ];

    protected $casts = [
        'dob' => 'date',
        'license_expiry' => 'date',
        'license_categories' => 'array',
        'is_verified' => 'boolean',
        'is_online' => 'boolean',
    ];


    public function documents(): HasMany
    {
        return $this->hasMany(DriverDocument::class);
    }

    public function vehicleAssignments(): HasMany
    {
        return $this->hasMany(DriverVehicleAssignment::class);
    }

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'driver_vehicle_assignments')
            ->withPivot(['assigned_from', 'assigned_to', 'is_current'])
            ->withTimestamps();
    }
}
