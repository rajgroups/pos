<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relations
            |--------------------------------------------------------------------------
            */

            // Driver / Owner
            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Sedan, SUV, Bike, Auto, Tractor etc
            $table->foreignId('vehicle_category_id')
                ->constrained('vehicle_categories')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Vehicle Details
            |--------------------------------------------------------------------------
            */

            $table->string('vehicle_number')->unique(); // TN01AB1234

            $table->string('brand')->nullable(); // Toyota
            $table->string('model')->nullable(); // Innova

            $table->string('color')->nullable();

            $table->year('manufacture_year')->nullable();

            /*
            |--------------------------------------------------------------------------
            | RC Details
            |--------------------------------------------------------------------------
            */

            $table->string('rc_number')->nullable();
            $table->date('rc_expiry')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Insurance
            |--------------------------------------------------------------------------
            */

            $table->string('insurance_number')->nullable();
            $table->date('insurance_expiry')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Permit Details
            |--------------------------------------------------------------------------
            */

            $table->string('permit_number')->nullable();
            $table->date('permit_expiry')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Fitness Certificate
            |--------------------------------------------------------------------------
            */

            $table->string('fitness_certificate_number')->nullable();
            $table->date('fitness_expiry')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Capacity
            |--------------------------------------------------------------------------
            */

            // Passenger vehicles
            $table->integer('seating_capacity')->nullable();

            // Goods / Transport vehicles
            $table->decimal('load_capacity', 10, 2)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Vehicle Images
            |--------------------------------------------------------------------------
            */

            $table->string('front_image')->nullable();
            $table->string('back_image')->nullable();
            $table->string('side_image')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'active',
                'inactive',
                'maintenance',
                'retired'
            ])->default('active');

            $table->boolean('is_verified')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
