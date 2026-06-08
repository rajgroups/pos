<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_categories', function (Blueprint $table) {
            $table->string('service_mode', 30)->default('instant')->after('type_key');
            $table->index(['service_mode', 'is_active']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('service_mode', 30)->default('instant')->after('vehicle_category_id');
            $table->timestamp('scheduled_at')->nullable()->after('service_mode');
            $table->decimal('duration_hours', 10, 2)->nullable()->after('scheduled_at');

            $table->index(['service_mode', 'status']);
            $table->index(['scheduled_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['service_mode', 'status']);
            $table->dropIndex(['scheduled_at', 'status']);
            $table->dropColumn(['service_mode', 'scheduled_at', 'duration_hours']);
        });

        Schema::table('vehicle_categories', function (Blueprint $table) {
            $table->dropIndex(['service_mode', 'is_active']);
            $table->dropColumn('service_mode');
        });
    }
};
