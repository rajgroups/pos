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
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('scheduled_processed_at')->nullable()->after('scheduled_at')->index();
            $table->timestamp('scheduled_notification_sent_at')->nullable()->after('scheduled_processed_at');
            $table->timestamp('driver_search_started_at')->nullable()->after('scheduled_notification_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['scheduled_processed_at']);
            $table->dropColumn([
                'scheduled_processed_at',
                'scheduled_notification_sent_at',
                'driver_search_started_at',
            ]);
        });
    }
};
