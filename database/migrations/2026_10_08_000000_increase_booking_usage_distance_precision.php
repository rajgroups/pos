<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_usage', function (Blueprint $table) {
            $table->decimal('distance_km', 12, 3)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('booking_usage', function (Blueprint $table) {
            $table->decimal('distance_km', 12, 2)->default(0)->change();
        });
    }
};
