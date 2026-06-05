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
        Schema::create('vehicle_category_pricings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_category_id')
                ->constrained()
                ->cascadeOnDelete();

            // Pricing Mode
            $table->enum('pricing_type', [
                'fixed',
                'distance',
                'hourly',
                'daily',
                'acre',
                'weight'
            ]);

            // Common Charges
            $table->decimal('base_fare', 12, 2)->default(0);
            $table->decimal('minimum_fare', 12, 2)->default(0);

            // Dynamic Charges
            $table->decimal('per_km_rate', 12, 2)->default(0);
            $table->decimal('per_hour_rate', 12, 2)->default(0);
            $table->decimal('per_day_rate', 12, 2)->default(0);
            $table->decimal('per_acre_rate', 12, 2)->default(0);
            $table->decimal('per_ton_rate', 12, 2)->default(0);

            // Extras
            $table->decimal('waiting_charge_per_hour', 12, 2)->default(0);
            $table->decimal('night_charge_percentage', 5, 2)->default(0);
            $table->decimal('surge_multiplier', 5, 2)->default(1);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_category_pricings');
    }
};
