<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'for_type',
        'has_expiry',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'has_expiry' => 'boolean',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function driverDocuments(): HasMany
    {
        return $this->hasMany(DriverDocument::class);
    }

    public function vehicleDocuments(): HasMany
    {
        return $this->hasMany(VehicleDocument::class);
    }

    public function vehicleTypeDocumentMaps(): HasMany
    {
        return $this->hasMany(VehicleTypeDocumentMap::class);
    }
}
