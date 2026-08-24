<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();
        DB::table('app_settings')->updateOrInsert(
            ['key' => 'driver_waiting_time'],
            [
                'value' => '3',
                'group' => 'dispatch',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('app_settings')->where('key', 'driver_waiting_time')->delete();
    }
};
