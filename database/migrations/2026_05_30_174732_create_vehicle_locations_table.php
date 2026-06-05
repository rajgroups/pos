<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_locations', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relations
            |--------------------------------------------------------------------------
            */

            $table->foreignId('vehicle_id')
                ->constrained('vehicles')
                ->cascadeOnDelete()
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | Current Location
            |--------------------------------------------------------------------------
            */

            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            /*
            |--------------------------------------------------------------------------
            | Movement Data
            |--------------------------------------------------------------------------
            */

            // km/h
            $table->decimal('speed', 8, 2)
                ->nullable();

            // Direction 0-360
            $table->decimal('heading', 8, 2)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | GPS Quality
            |--------------------------------------------------------------------------
            */

            // GPS accuracy in meters
            $table->decimal('accuracy', 8, 2)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Availability
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_online')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            // Actual GPS timestamp from mobile
            $table->timestamp('location_updated_at')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index(['latitude', 'longitude']);
            $table->index('is_online');
            $table->index('location_updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_locations');
    }
};
