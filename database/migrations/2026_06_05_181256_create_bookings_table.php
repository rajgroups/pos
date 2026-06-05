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

            $table->uuid('booking_no')->unique();

            $table->foreignId('user_id')->constrained();

            $table->foreignId('driver_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('vehicle_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('vehicle_category_id')
                ->constrained();

            // OTP
            $table->string('start_otp', 6)->nullable();
            $table->timestamp('otp_verified_at')->nullable();

            // Status
            $table->enum('status', [
                'pending',
                'accepted',
                'started',
                'completed',
                'cancelled'
            ])->default('pending');

            // Estimated
            $table->decimal('estimated_amount', 12, 2)->default(0);

            // Final
            $table->decimal('final_amount', 12, 2)->default(0);

            // Payment
            $table->enum('payment_status', [
                'pending',
                'paid',
                'failed',
                'refunded'
            ])->default('pending');

            $table->enum('payment_method', [
                'cash',
                'online',
                'wallet'
            ])->nullable();

            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index([
                'status',
                'vehicle_category_id'
            ]);
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
