<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enum\Status;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $fillable = [
        'name',
        'shortname',
        'no_of_product',
        'type',
        'description',
        'status'
    ];

    protected $casts = [
        'status'        => Status::class,
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'no_of_product' => 'integer',
    ];

    // Constants for unit types
    public const TYPE_WEIGHT = 'weight';
    public const TYPE_LENGTH = 'length';
    public const TYPE_VOLUME = 'volume';
    public const TYPE_QUANTITY = 'quantity';

    public const TYPES = [
        self::TYPE_WEIGHT,
        self::TYPE_LENGTH,
        self::TYPE_VOLUME,
        self::TYPE_QUANTITY,
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', Status::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', Status::INACTIVE);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeWeightUnits($query)
    {
        return $query->where('type', self::TYPE_WEIGHT);
    }

    public function scopeLengthUnits($query)
    {
        return $query->where('type', self::TYPE_LENGTH);
    }

    public function scopeVolumeUnits($query)
    {
        return $query->where('type', self::TYPE_VOLUME);
    }

    public function scopeQuantityUnits($query)
    {
        return $query->where('type', self::TYPE_QUANTITY);
    }

    // Accessors
    public function getFormattedNameAttribute(): string
    {
        return $this->name . ' (' . $this->shortname . ')';
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === Status::ACTIVE;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getIsWeightAttribute(): bool
    {
        return $this->type === self::TYPE_WEIGHT;
    }

    public function getIsLengthAttribute(): bool
    {
        return $this->type === self::TYPE_LENGTH;
    }

    public function getIsVolumeAttribute(): bool
    {
        return $this->type === self::TYPE_VOLUME;
    }

    public function getIsQuantityAttribute(): bool
    {
        return $this->type === self::TYPE_QUANTITY;
    }

    // Validation rules
    public static function validationRules(int $id = null): array
    {
        return [
            'name' => 'required|string|max:255|unique:units,name,' . $id,
            'shortname' => 'required|string|max:10',
            'type' => 'required|in:' . implode(',', self::TYPES),
            'status' => 'required|in:' . implode(',', Status::getValues()),
            'no_of_product' => 'nullable|integer|min:0',
        ];
    }

    // Helper methods
    public static function getUnitsByType(string $type, bool $activeOnly = true)
    {
        $query = self::where('type', $type);

        if ($activeOnly) {
            $query->active();
        }

        return $query->orderBy('name')->get();
    }

    public static function getAllWeightUnits(bool $activeOnly = true)
    {
        return self::getUnitsByType(self::TYPE_WEIGHT, $activeOnly);
    }

    public static function getAllLengthUnits(bool $activeOnly = true)
    {
        return self::getUnitsByType(self::TYPE_LENGTH, $activeOnly);
    }

    public static function getAllVolumeUnits(bool $activeOnly = true)
    {
        return self::getUnitsByType(self::TYPE_VOLUME, $activeOnly);
    }

    public static function getAllQuantityUnits(bool $activeOnly = true)
    {
        return self::getUnitsByType(self::TYPE_QUANTITY, $activeOnly);
    }

    // Status management
    public function activate(): bool
    {
        return $this->update(['status' => Status::ACTIVE]);
    }

    public function deactivate(): bool
    {
        return $this->update(['status' => Status::INACTIVE]);
    }

    public function toggleStatus(): bool
    {
        return $this->update([
            'status' => $this->is_active ? Status::INACTIVE : Status::ACTIVE
        ]);
    }
}
