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
        Schema::create('wali_nagaris', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 50);
            // Foreign key to User table, enforcing one-to-one by adding unique constraint
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // Enforcing unique constraint to ensure one WaliNagari per User
            $table->unique('user_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wali_nagaris');
    }
};
