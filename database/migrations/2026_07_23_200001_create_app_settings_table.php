<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('group', 50)->default('general')->index();
            $table->timestamps();
        });

        // Insert initial configuration rows for User App & Driver App
        $now = now();
        DB::table('app_settings')->insert([
            // User App Settings
            [
                'key' => 'user_app_latest_version',
                'value' => '1.0.0',
                'group' => 'user_app',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'user_app_min_version',
                'value' => '1.0.0',
                'group' => 'user_app',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'user_app_force_update',
                'value' => '0',
                'group' => 'user_app',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'user_app_url_android',
                'value' => 'https://play.google.com/store/apps/details?id=com.indicab.user',
                'group' => 'user_app',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'user_app_url_ios',
                'value' => 'https://apps.apple.com/app/indicab-user/id123456789',
                'group' => 'user_app',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'user_app_update_title',
                'value' => 'New Update Available!',
                'group' => 'user_app',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'user_app_update_message',
                'value' => 'We have added exciting new features and performance enhancements. Please update to the latest version for the best experience.',
                'group' => 'user_app',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Driver App Settings
            [
                'key' => 'driver_app_latest_version',
                'value' => '1.0.0',
                'group' => 'driver_app',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'driver_app_min_version',
                'value' => '1.0.0',
                'group' => 'driver_app',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'driver_app_force_update',
                'value' => '0',
                'group' => 'driver_app',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'driver_app_url_android',
                'value' => 'https://play.google.com/store/apps/details?id=com.indicab.driver',
                'group' => 'driver_app',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'driver_app_url_ios',
                'value' => 'https://apps.apple.com/app/indicab-driver/id123456789',
                'group' => 'driver_app',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'driver_app_update_title',
                'value' => 'Driver App Update Available!',
                'group' => 'driver_app',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'driver_app_update_message',
                'value' => 'A new version of Indicab Driver is available. Please update to ensure seamless booking acceptance and navigation.',
                'group' => 'driver_app',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
