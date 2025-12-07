<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stores';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'store_name',
        'slug',
        'owner_name',
        'email',
        'phone',
        'address',
        'logo',
        'banner',
        'gallery',
        'description',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'opening_hours',
        'latitude',
        'longitude',
        'timezone',
        'currency',
        'tax_id',
        'website',
        'social_media',
    ];

    /**
     * Guarded attributes (cannot be mass assigned)
     */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Default attribute values
     */
    protected $attributes = [
        'status' => true,
        'gallery' => '[]',
        'social_media' => '{}',
        'meta_keywords' => '[]',
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'gallery' => 'array',
        'social_media' => 'array',
        'meta_keywords' => 'array',
        'status' => 'boolean',
        'opening_hours' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Appends virtual attributes
     */
    protected $appends = [
        'logo_url',
        'banner_url',
        'gallery_urls',
        'is_active',
    ];

    /**
     * Hidden attributes for serialization
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Generate slug before creating
        static::creating(function ($store) {
            if (empty($store->slug)) {
                $store->slug = Str::slug($store->store_name);
            }
        });

        // Update slug if store name changes
        static::updating(function ($store) {
            if ($store->isDirty('store_name') && empty($store->slug)) {
                $store->slug = Str::slug($store->store_name);
            }
        });

        // Clean up files when deleting
        static::deleting(function ($store) {
            if ($store->isForceDeleting()) {
                $store->deleteFiles();
            }
        });
    }

    /**
     * Accessor for logo URL
     */
    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->logo)) {
                    return asset('images/default-store-logo.png');
                }

                if (filter_var($this->logo, FILTER_VALIDATE_URL)) {
                    return $this->logo;
                }

                return Storage::disk('public')->url($this->logo);
            }
        );
    }

    /**
     * Accessor for banner URL
     */
    protected function bannerUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->banner)) {
                    return asset('images/default-store-banner.jpg');
                }

                if (filter_var($this->banner, FILTER_VALIDATE_URL)) {
                    return $this->banner;
                }

                return Storage::disk('public')->url($this->banner);
            }
        );
    }

    /**
     * Accessor for gallery URLs
     */
    protected function galleryUrls(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->gallery)) {
                    return [];
                }

                return collect($this->gallery)->map(function ($image) {
                    if (filter_var($image, FILTER_VALIDATE_URL)) {
                        return $image;
                    }
                    return Storage::disk('public')->url($image);
                })->toArray();
            }
        );
    }

    /**
     * Accessor for is_active
     */
    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === true
        );
    }

    /**
     * Mutator for store name (trim and ucwords)
     */
    protected function storeName(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => ucwords(trim($value)),
        );
    }

    /**
     * Mutator for email (lowercase)
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => strtolower(trim($value)),
        );
    }

    /**
     * Mutator for phone (clean formatting)
     */
    protected function phone(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                // Remove all non-numeric characters except +
                return preg_replace('/[^0-9+]/', '', trim($value));
            },
        );
    }

    /**
     * Mutator for slug (ensure uniqueness)
     */
    protected function slug(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                $slug = \Str::slug($value);
                $count = self::where('slug', 'LIKE', "{$slug}%")
                    ->where('id', '!=', $this->id ?? null)
                    ->count();

                return $count ? "{$slug}-{$count}" : $slug;
            },
        );
    }

    /**
     * Delete associated files
     */
    public function deleteFiles(): void
    {
        // Delete logo
        if ($this->logo && !filter_var($this->logo, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($this->logo);
        }

        // Delete banner
        if ($this->banner && !filter_var($this->banner, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($this->banner);
        }

        // Delete gallery images
        if ($this->gallery) {
            foreach ($this->gallery as $image) {
                if (!filter_var($image, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($image);
                }
            }
        }
    }

    /**
     * Scope: Active stores
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope: Inactive stores
     */
    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    /**
     * Scope: Search by name or email
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('store_name', 'LIKE', "%{$search}%")
              ->orWhere('owner_name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('phone', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope: Order by creation date
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Check if store is currently open
     * (Basic implementation - customize as needed)
     */
    public function isOpen(): bool
    {
        if (empty($this->opening_hours)) {
            return true;
        }

        $now = now($this->timezone ?? config('app.timezone'));
        $day = strtolower($now->format('l'));

        $hours = $this->opening_hours[$day] ?? null;

        if (!$hours || !$hours['open']) {
            return false;
        }

        $currentTime = $now->format('H:i');
        return $currentTime >= $hours['open'] && $currentTime <= $hours['close'];
    }

    /**
     * Get formatted opening hours
     */
    public function getFormattedOpeningHours(): array
    {
        if (empty($this->opening_hours)) {
            return [];
        }

        $days = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];

        $formatted = [];
        foreach ($days as $key => $day) {
            $hours = $this->opening_hours[$key] ?? null;
            if ($hours && $hours['open']) {
                $formatted[$day] = "{$hours['open']} - {$hours['close']}";
            } else {
                $formatted[$day] = 'Closed';
            }
        }

        return $formatted;
    }

    /**
     * Relationship with users (store staff)
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relationship with products
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relationship with orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relationship with categories
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }
    public static function validationRules($id = null)
    {
        return [
            'store_name'        => 'required|string|max:255',
            'slug'              => 'nullable|string|max:255|unique:stores,slug' . ($id ? ',' . $id : ''),
            'owner_name'        => 'nullable|string|max:255',
            'email'             => 'nullable|email|max:255',
            'phone'             => 'nullable|string|max:20',
            'address'           => 'nullable|string',
            'website'           => 'nullable|url|max:255',
            'tax_id'            => 'nullable|string|max:100',
            'currency'          => 'nullable|string|max:10',

            'latitude'          => 'nullable|numeric',
            'longitude'         => 'nullable|numeric',

            'timezone'          => 'nullable|string|max:64',

            'logo'              => 'nullable|mimes:png,jpg,jpeg,webp|max:2048',
            'banner'            => 'nullable|mimes:png,jpg,jpeg,webp|max:4096',

            'gallery.*'         => 'nullable|mimes:png,jpg,jpeg,webp|max:4096',
            'gallery'           => 'nullable|array|max:10',

            'opening_hours'     => 'nullable|array',
            'social_media'      => 'nullable|array',

            'description'       => 'nullable|string',

            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string',
            'meta_keywords'     => 'nullable',

            'status'            => 'nullable|boolean',
        ];
    }

}
