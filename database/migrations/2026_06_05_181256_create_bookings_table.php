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
        Schema::create('bookings', function (Blueprint $table) {

            $table->id();

            $table->ulid('booking_no')->unique();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('drivers')
                ->nullOnDelete();

            $table->foreignId('vehicle_id')
                ->nullable()
                ->constrained('vehicles')
                ->nullOnDelete();

            $table->foreignId('vehicle_category_id')
                ->constrained('vehicle_categories')
                ->restrictOnDelete();

            // Service Mode & Scheduling
            $table->string('service_mode', 30)->default('instant')->index();
            $table->timestamp('scheduled_at')->nullable()->index();

            // OTP
            $table->string('start_otp', 10)->nullable();
            $table->timestamp('otp_verified_at')->nullable();

            // Status
            $table->string('status', 30)->default('pending')->index();

            // Estimated
            $table->decimal('estimated_amount', 12, 2)->default(0);

            // Final
            $table->decimal('final_amount', 12, 2)->default(0);

            // Payment
            $table->string('payment_status', 30)->default('pending')->index();

            $table->string('payment_method', 30)->nullable();

            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'vehicle_category_id']);
            $table->index(['user_id', 'status']);
            $table->index(['driver_id', 'status']);
            $table->index(['vehicle_category_id', 'status']);
            $table->index(['service_mode', 'scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
