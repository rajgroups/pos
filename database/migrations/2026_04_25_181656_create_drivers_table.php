<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('name');
            $table->string('phone', 20)->unique();
            $table->string('otp')->nullable();
            $table->string('email')->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();

            // Address
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();

            // Identification
            $table->string('aadhaar_number')->nullable();
            $table->string('pan_number')->nullable();

            // License Details
            $table->string('license_number')->unique();
            $table->date('license_expiry')->nullable();
            $table->json('license_categories')->nullable();
            // Example: ["LMV", "HMV", "TR", "Bike", "Tractor"]

            // Driver Status
            $table->enum('driver_type', [
                'car',
                'bike',
                'auto',
                'borewell',
                'tractor',
                'harvester',
                'lorry',
                'mini_van',
                'bus',
                'other'
            ])->nullable();

            // Employment / Platform Status
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');
            $table->boolean('is_verified')->default(false);

            // Documents / Uploads
            $table->string('profile_photo')->nullable();
            $table->string('license_front')->nullable();
            $table->string('license_back')->nullable();
            $table->string('aadhaar_front')->nullable();
            $table->string('aadhaar_back')->nullable();
            $table->string('pan_card_file')->nullable();
            $table->string('police_verification_file')->nullable();
            $table->string('medical_certificate')->nullable();

            // Optional Notes
            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
