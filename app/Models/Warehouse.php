<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'warehouses';

    protected $fillable = [
        'warehouse_name',
        'warehouse_code',
        'warehouse_type',
        'contact_person',
        'email',
        'email_alt',
        'phone',
        'phone_work',
        'map_link',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'capacity',
        'opening_hours',
        'notes',
        'status',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'capacity' => 'integer',
        'status' => 'boolean',
    ];

    protected $attributes = [
        'status' => true,
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // Validation rules (used by ValidationHelper)
    public static function rules($id = null)
    {
        return [
            'warehouse_name' => 'required|string|max:191',
            'warehouse_code' => 'required|string|max:100|unique:warehouses,warehouse_code' . ($id ? ",$id" : ''),
            'warehouse_type' => 'nullable|string|max:100',
            'contact_person' => 'nullable|string|max:191',
            'email' => 'nullable|email|max:191',
            'email_alt' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:50',
            'phone_work' => 'nullable|string|max:50',
            'map_link' => 'nullable|url|max:1000',
            'address' => 'required|string|max:1000',
            'city' => 'nullable|string|max:191',
            'state' => 'nullable|string|max:191',
            'country' => 'nullable|string|max:191',
            'postal_code' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'capacity' => 'nullable|integer|min:0',
            'opening_hours' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
            'status' => 'boolean',
        ];
    }
}
