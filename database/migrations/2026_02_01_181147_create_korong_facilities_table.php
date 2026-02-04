<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\FacilityType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('korong_facilities', function (Blueprint $table) {
            $table->id();

            // Facility Information
            $table->string('name');
            $table->enum('type', FacilityType::all())->default(FacilityType::LAINNYA);
            $table->text('description')->nullable();

            // Location Details
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Contact Information
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();

            // Status & Metadata
            $table->boolean('is_active')->default(true);
            $table->date('established_date')->nullable();
            $table->integer('capacity')->nullable()->comment('Kapasitas fasilitas');

            // Relationships
            $table->foreignId('korong_id')->constrained('korongs')->cascadeOnDelete();
            $table->foreignId('facility_manager_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('korong_facilities');
    }
};
