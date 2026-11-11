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
        Schema::create('therapist_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('qualification')->nullable();
            $table->string('specialization')->default('General Physiotherapy');
            $table->string('license_number')->nullable();
            $table->integer('experience_years')->default(0);
            $table->integer('slot_duration_minutes')->default(45);
            $table->decimal('consultation_fee', 10, 2)->default(500.00);
            $table->decimal('home_visit_fee', 10, 2)->default(800.00);
            $table->boolean('is_available_for_home_visits')->default(true);
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapist_profiles');
    }
};
