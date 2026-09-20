<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_maps_usage_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('service', 100)->comment('e.g. places, directions, geocoding, maps_sdk');
            $table->string('sku', 200)->nullable()->comment('Specific SKU identifier');
            $table->unsignedBigInteger('requests')->default(0);
            $table->unsignedBigInteger('billable_requests')->default(0);
            $table->decimal('cost', 12, 4)->default(0);
            $table->string('currency', 3)->default('INR');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['date', 'service', 'sku'], 'gmaps_date_service_sku');
            $table->index('date');
            $table->index('service');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_maps_usage_snapshots');
    }
};
