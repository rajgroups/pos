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
        Schema::create('users', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Primary
            |--------------------------------------------------------------------------
            */
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Basic Authentication
            |--------------------------------------------------------------------------
            */
            $table->string('uuid')->nullable()->unique();

            $table->string('name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('username')->nullable()->unique();

            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('mobile')->nullable()->unique();
            $table->string('country_code', 10)->nullable();
            $table->timestamp('mobile_verified_at')->nullable();

            $table->string('password')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Profile Information
            |--------------------------------------------------------------------------
            */
            $table->string('profile_image')->nullable();
            $table->string('cover_image')->nullable();

            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();

            $table->string('language')->nullable();
            $table->string('timezone')->nullable();

            $table->text('bio')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Role System
            |--------------------------------------------------------------------------
            | Flexible for future:
            | customer, driver, owner, vendor, farmer, mechanic, partner etc
            |--------------------------------------------------------------------------
            */
            $table->string('primary_role')->nullable();

            $table->json('roles')->nullable();

            /*
            |--------------------------------------------------------------------------
            | User Type / Business Type
            |--------------------------------------------------------------------------
            */
            $table->string('account_type')->nullable();
            $table->string('business_name')->nullable();
            $table->string('business_type')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Address Details
            |--------------------------------------------------------------------------
            */
            $table->string('address')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();

            $table->string('landmark')->nullable();

            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();

            $table->string('postal_code')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Government / Legal
            |--------------------------------------------------------------------------
            */
            $table->string('aadhaar_number')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('gst_number')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Driver Details
            |--------------------------------------------------------------------------
            */
            $table->string('driving_license_number')->nullable();
            $table->date('driving_license_expiry')->nullable();

            $table->string('driving_license_front')->nullable();
            $table->string('driving_license_back')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Identity Documents
            |--------------------------------------------------------------------------
            */
            $table->string('aadhaar_front')->nullable();
            $table->string('aadhaar_back')->nullable();

            $table->string('pan_card_image')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Banking / Wallet
            |--------------------------------------------------------------------------
            */
            $table->string('bank_name')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_ifsc')->nullable();

            $table->string('upi_id')->nullable();

            $table->decimal('wallet_balance', 12, 2)->nullable()->default(0);

            /*
            |--------------------------------------------------------------------------
            | Emergency Contact
            |--------------------------------------------------------------------------
            */
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_mobile')->nullable();
            $table->string('emergency_contact_relation')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Device / App Information
            |--------------------------------------------------------------------------
            */
            $table->string('device_type')->nullable();
            $table->string('device_token')->nullable();

            $table->string('app_version')->nullable();
            $table->string('platform')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Social Login
            |--------------------------------------------------------------------------
            */
            $table->string('google_id')->nullable();
            $table->string('facebook_id')->nullable();
            $table->string('apple_id')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status / Verification
            |--------------------------------------------------------------------------
            */
            $table->boolean('is_email_verified')->nullable()->default(false);
            $table->boolean('is_mobile_verified')->nullable()->default(false);
            $table->boolean('is_document_verified')->nullable()->default(false);
            $table->boolean('is_driver_verified')->nullable()->default(false);
            $table->boolean('is_business_verified')->nullable()->default(false);

            $table->boolean('is_active')->nullable()->default(true);
            $table->boolean('is_blocked')->nullable()->default(false);

            /*
            |--------------------------------------------------------------------------
            | Referral / Promo
            |--------------------------------------------------------------------------
            */
            $table->string('referral_code')->nullable();
            $table->unsignedBigInteger('referred_by')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Subscription / Membership
            |--------------------------------------------------------------------------
            */
            $table->string('subscription_type')->nullable();
            $table->timestamp('subscription_start_at')->nullable();
            $table->timestamp('subscription_end_at')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Preferences
            |--------------------------------------------------------------------------
            */
            $table->json('preferences')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Future Expansion
            |--------------------------------------------------------------------------
            */
            $table->json('extra_data')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Security
            |--------------------------------------------------------------------------
            */
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();

            $table->rememberToken();

            $table->timestamps();
            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */
            $table->index('mobile');
            $table->index('email');
            $table->index('primary_role');
            $table->index('is_active');
            $table->index('is_blocked');

            /*
            |--------------------------------------------------------------------------
            | Self Relation
            |--------------------------------------------------------------------------
            */
            $table->foreign('referred_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
