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
        Schema::create('sms_gateway_devices', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('name')->nullable();
            $blueprint->string('device_identifier')->unique();
            $blueprint->string('phone_number')->nullable();
            $blueprint->string('status')->default('active'); // active, inactive
            $blueprint->string('token_hash');
            $blueprint->timestamp('last_seen_at')->nullable();
            $blueprint->timestamps();
        });

        Schema::create('sms_gateway_messages', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('gateway_device_id')->nullable()->constrained('sms_gateway_devices')->onDelete('set null');
            $blueprint->string('to');
            $blueprint->text('message');
            $blueprint->string('status')->default('pending'); // pending, processing, sent, failed, expired
            $blueprint->string('idempotency_key')->unique();
            $blueprint->integer('attempts')->default(0);
            $blueprint->timestamp('expires_at');
            $blueprint->timestamp('sent_at')->nullable();
            $blueprint->timestamp('failed_at')->nullable();
            $blueprint->text('error_message')->nullable();
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_gateway_messages');
        Schema::dropIfExists('sms_gateway_devices');
    }
};
