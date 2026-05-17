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
        Schema::create('vehicle_categories', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Parent Category
            |--------------------------------------------------------------------------
            | Example:
            | Car
            |   ├── Sedan
            |   ├── SUV
            | Bike
            |   ├── Sports Bike
            |   ├── Scooter
            */
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('vehicle_categories')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Basic Details
            |--------------------------------------------------------------------------
            */
            $table->string('type_key')->nullable()->index();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('tagline')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Media
            |--------------------------------------------------------------------------
            */
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->string('accent_color')->nullable();
            $table->string('gradient_start')->nullable();
            $table->string('gradient_end')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Pricing
            |--------------------------------------------------------------------------
            */
            $table->decimal('base_fare', 10, 2)->nullable();
            $table->decimal('per_km_rate', 10, 2)->nullable();
            $table->string('price_label')->nullable();
            $table->string('starting_fare')->nullable();
            $table->string('eta')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Vehicle Information
            |--------------------------------------------------------------------------
            */
            $table->integer('max_capacity')->nullable();
            $table->unsignedInteger('sort_order')->default(0);

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_categories');
    }
};
