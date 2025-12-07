<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariantAttribute extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'variant_attributes';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'values',
        'description',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'values' => 'array',       // JSON → Array
        'status' => 'boolean',
    ];

    /**
     * Variant types
     */
    public const TYPE_TEXT     = 'text';
    public const TYPE_COLOR    = 'color';
    public const TYPE_SIZE     = 'size';
    public const TYPE_MATERIAL = 'material';

    public const TYPES = [
        self::TYPE_TEXT,
        self::TYPE_COLOR,
        self::TYPE_SIZE,
        self::TYPE_MATERIAL,
    ];

    /**
     * Validation Rules
     */
    public static function validationRules($id = null): array
    {
        return [
            'name'        => 'required|string|max:255|unique:variant_attributes,name,' . $id,
            'slug'        => 'required|string|max:255|unique:variant_attributes,slug,' . $id,
            'type'        => 'required|in:' . implode(',', self::TYPES),
            'values'      => 'required', // validated in controller (comma-separated or json)
            'sort_order'  => 'nullable|integer|min:0',
            'status'      => 'required|boolean',
        ];
    }

    /**
     * Accessor: Convert comma separated values to array automatically
     */
    public function setValuesAttribute($val)
    {
        if (is_string($val)) {
            $this->attributes['values'] = json_encode(array_map('trim', explode(',', $val)));
        } else {
            $this->attributes['values'] = json_encode($val);
        }
    }

    /**
     * Accessor to show formatted values
     */
    public function getFormattedValuesAttribute(): string
    {
        return implode(', ', $this->values ?? []);
    }

    /**
     * Helper to check active status
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === true;
    }

    /**
     * Status label
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    /**
     * Type labels for UI
     */
    public function getTypeLabelAttribute(): string
    {
        return ucfirst($this->type);
    }
}
