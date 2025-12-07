<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warranty extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'warranty',
        'type',
        'code',
        'duration',
        'period',
        'start_after',
        'lifetime',
        'max_claims',
        'replacement_allowed',
        'description',
        'terms',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'lifetime' => 'boolean',
        'replacement_allowed' => 'boolean',
        'status' => 'boolean',
        'duration' => 'integer',
        'start_after' => 'integer',
        'max_claims' => 'integer',
    ];

    /**
     * Default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'start_after' => 0,
        'lifetime' => false,
        'replacement_allowed' => false,
        'status' => true,
    ];

    /**
     * Scope a query to only include active warranties.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to only include lifetime warranties.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLifetime($query)
    {
        return $query->where('lifetime', true);
    }

    /**
     * Scope a query to only include limited warranties.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLimited($query)
    {
        return $query->where('lifetime', false);
    }

    /**
     * Get the warranty period in days.
     *
     * @return int|null
     */
    public function getPeriodInDaysAttribute()
    {
        if ($this->lifetime) {
            return null; // Lifetime warranty has no expiry
        }

        if (!$this->duration || !$this->period) {
            return null;
        }

        $multiplier = match ($this->period) {
            'Day' => 1,
            'Week' => 7,
            'Month' => 30, // Approximation
            'Year' => 365,
            default => 1,
        };

        return $this->duration * $multiplier;
    }

    /**
     * Check if warranty is expired based on purchase date.
     *
     * @param  string  $purchaseDate
     * @return bool
     */
    public function isExpired($purchaseDate)
    {
        if ($this->lifetime) {
            return false; // Lifetime never expires
        }

        if (!$this->duration || !$this->period) {
            return false;
        }

        $purchaseDate = \Carbon\Carbon::parse($purchaseDate);
        $startDate = $purchaseDate->addDays($this->start_after);
        $expiryDate = $startDate->addDays($this->period_in_days);

        return now()->gt($expiryDate);
    }

    /**
     * Get the display name of the warranty.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        if ($this->lifetime) {
            return "{$this->warranty} (Lifetime)";
        }

        if ($this->duration && $this->period) {
            return "{$this->warranty} ({$this->duration} {$this->period})";
        }

        return $this->warranty;
    }

    /**
     * Relationship with products.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relationship with warranty claims.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function claims()
    {
        return $this->hasMany(WarrantyClaim::class);
    }

    /**
     * Validation rules for creating/updating warranty.
     *
     * @return array
     */
    public static function rules($id = null)
    {
        $rules = [
            'warranty' => 'required|string|max:255|unique:warranties,warranty' . ($id ? ",$id" : ''),
            'type' => 'required|in:Standard,Extended,Replacement,Service Warranty',
            'code' => 'nullable|string|max:100|unique:warranties,code' . ($id ? ",$id" : ''),
            'duration' => 'nullable|integer|min:1|required_unless:lifetime,1',
            'period' => 'nullable|in:Day,Week,Month,Year|required_unless:lifetime,1',
            'start_after' => 'integer|min:0',
            'lifetime' => 'boolean',
            'max_claims' => 'nullable|integer|min:1',
            'replacement_allowed' => 'boolean',
            'description' => 'required|string|min:10|max:1000',
            'terms' => 'nullable|string|max:5000',
            'status' => 'boolean',
        ];

        // Custom validation for lifetime warranty
        if (request()->input('lifetime') == 1) {
            $rules['duration'] = 'nullable';
            $rules['period'] = 'nullable';
        }

        return $rules;
    }

    /**
     * Custom error messages for validation.
     *
     * @return array
     */
    public static function messages()
    {
        return [
            'warranty.required' => 'Warranty name is required.',
            'warranty.unique' => 'This warranty name already exists.',
            'type.required' => 'Warranty type is required.',
            'type.in' => 'Please select a valid warranty type.',
            'code.unique' => 'This warranty code already exists.',
            'duration.required_unless' => 'Duration is required unless this is a lifetime warranty.',
            'period.required_unless' => 'Period is required unless this is a lifetime warranty.',
            'duration.integer' => 'Duration must be a number.',
            'duration.min' => 'Duration must be at least 1.',
            'start_after.integer' => 'Start after days must be a number.',
            'start_after.min' => 'Start after days cannot be negative.',
            'max_claims.integer' => 'Maximum claims must be a number.',
            'max_claims.min' => 'Maximum claims must be at least 1.',
            'description.required' => 'Description is required.',
            'description.min' => 'Description must be at least 10 characters.',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear duration and period for lifetime warranty
        static::saving(function ($warranty) {
            if ($warranty->lifetime) {
                $warranty->duration = null;
                $warranty->period = null;
            }
        });
    }
}
