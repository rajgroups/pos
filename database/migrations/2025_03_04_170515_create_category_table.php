<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\status;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();
            $table->string('slug')->unique();

            $table->unsignedBigInteger('parent_id')->nullable();

            $table->longText('image')->nullable();
            $table->longText('icon')->nullable();

            $table->enum('status', array_column(status::cases(), 'value'));

            $table->timestamps();

            /** Self Relationship (parent → child category) */
            $table->foreign('parent_id')
                ->references('id')
                ->on('category')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category');
    }
};
