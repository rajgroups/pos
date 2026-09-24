<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add driver_search_radius_km to vehicle_categories.
 *
 * Each category/subcategory can have a different search radius.
 * Default 5 km is backward-compatible with existing data.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_categories', function (Blueprint $table) {
            $table->decimal('driver_search_radius_km', 8, 2)
                ->default(5.00)
                ->after('drop_location_required')
                ->comment('Max driver search radius in kilometres for this category');

            $table->index('driver_search_radius_km');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_categories', function (Blueprint $table) {
            $table->dropIndex(['driver_search_radius_km']);
            $table->dropColumn('driver_search_radius_km');
        });
    }
};
