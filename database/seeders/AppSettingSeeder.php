<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            // User App Settings
            [
                'key' => 'user_app_latest_version',
                'value' => '1.0.0',
                'group' => 'user_app',
            ],
            [
                'key' => 'user_app_min_version',
                'value' => '1.0.0',
                'group' => 'user_app',
            ],
            [
                'key' => 'user_app_force_update',
                'value' => '0',
                'group' => 'user_app',
            ],
            [
                'key' => 'user_app_url_android',
                'value' => 'https://play.google.com/store/apps/details?id=com.indicab.user',
                'group' => 'user_app',
            ],
            [
                'key' => 'user_app_url_ios',
                'value' => 'https://apps.apple.com/app/indicab-user/id12345678',
                'group' => 'user_app',
            ],
            [
                'key' => 'user_app_update_title',
                'value' => 'New Update Available!',
                'group' => 'user_app',
            ],
            [
                'key' => 'user_app_update_message',
                'value' => 'We have added exciting new features and performance improvements.',
                'group' => 'user_app',
            ],
            
            // Driver App Settings
            [
                'key' => 'driver_app_latest_version',
                'value' => '1.0.0',
                'group' => 'driver_app',
            ],
            [
                'key' => 'driver_app_min_version',
                'value' => '1.0.0',
                'group' => 'driver_app',
            ],
            [
                'key' => 'driver_app_force_update',
                'value' => '0',
                'group' => 'driver_app',
            ],
            [
                'key' => 'driver_app_url_android',
                'value' => 'https://play.google.com/store/apps/details?id=com.indicab.driver',
                'group' => 'driver_app',
            ],
            [
                'key' => 'driver_app_url_ios',
                'value' => 'https://apps.apple.com/app/indicab-driver/id123456',
                'group' => 'driver_app',
            ],
            [
                'key' => 'driver_app_update_title',
                'value' => 'Driver App Update Available!',
                'group' => 'driver_app',
            ],
            [
                'key' => 'driver_app_update_message',
                'value' => 'A new version of Indicab Driver is available. Please update.',
                'group' => 'driver_app',
            ],

            // Dispatch Settings
            [
                'key' => 'driver_waiting_time',
                'value' => '3',
                'group' => 'dispatch',
            ],
        ];

        foreach ($settings as $setting) {
            AppSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
