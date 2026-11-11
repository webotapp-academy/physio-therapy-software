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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('therapist_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->date('assessment_date');
            $table->text('chief_complaint');
            $table->tinyInteger('pain_scale_vas')->default(0); // 0 - 10
            $table->json('pain_body_areas')->nullable(); // e.g. ["Lower Back", "Neck", "Right Knee"]
            $table->date('onset_date')->nullable();
            $table->text('history_of_present_illness')->nullable();
            $table->text('posture_gait_assessment')->nullable();
            $table->text('range_of_motion_notes')->nullable();
            $table->text('muscle_power_mmt_notes')->nullable();
            $table->text('special_tests_results')->nullable();
            $table->text('provisional_diagnosis');
            $table->text('treatment_goals_short')->nullable();
            $table->text('treatment_goals_long')->nullable();
            $table->integer('recommended_sessions_count')->default(10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
