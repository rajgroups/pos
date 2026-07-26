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
        if (! Schema::hasColumn('drivers', 'fcm_token')) {
            Schema::table('drivers', function (Blueprint $table) {
                $table->text('fcm_token')->nullable()->after('is_online');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('drivers', 'fcm_token')) {
            Schema::table('drivers', function (Blueprint $table) {
                $table->dropColumn('fcm_token');
            });
        }
    }
};
