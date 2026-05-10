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
        Schema::create('driver_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
$table->foreignId('document_type_id')
    ->constrained('document_types')
    ->cascadeOnDelete();
            /*
        aadhaar
        pan
        license
        police_verification
        medical_certificate
    */

            $table->string('document_number')->nullable();
            $table->string('file_path');

            $table->date('expiry_date')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_documents');
    }
};
