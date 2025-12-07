<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();

            // Basic info
            $table->string('warehouse_name');
            $table->string('warehouse_code')->unique();
            $table->string('warehouse_type')->nullable();

            // Contact
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('email_alt')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_work')->nullable();
            $table->string('map_link')->nullable();

            // Address
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();

            // Location & capacity
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('capacity')->nullable(); // in sq ft

            // Operational
            $table->string('opening_hours')->nullable();
            $table->text('notes')->nullable();

            // Claims/status
            $table->boolean('status')->default(1);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
