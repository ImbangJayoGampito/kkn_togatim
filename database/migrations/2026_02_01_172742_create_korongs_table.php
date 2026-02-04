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
        Schema::create('korongs', function (Blueprint $table) {
            $table->id();
            // General Information
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('phone', 50);
            $table->string('email');
            $table->text('description')->nullable();
            // Geolocation
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            // Demographic and Geographic Data
            $table->integer('total_households')->default(0);
            $table->integer('total_korongs')->default(0);
            $table->integer('male_population')->default(0);
            $table->integer('female_population')->default(0);


            $table->float('area_size_km2')->nullable()->comment('Luas wilayah dalam km²');
            $table->json('population_data')->nullable()->comment('Data populasi terstruktur');
            // Relationships
            $table->foreignId('wali_korong_id')->nullable()->constrained('wali_korongs')->nullOnDelete();
            $table->foreignId('nagari_id')->constrained('nagaris')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('korongs');
    }
};
