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
        Schema::create('treatment_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('therapist_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('session_number')->default(1);
            $table->date('session_date');
            $table->enum('service_location', ['clinic', 'home_visit'])->default('clinic');
            $table->json('modalities_used')->nullable(); // e.g. ["TENS", "Ultrasound", "IFT", "Manual Therapy"]
            $table->text('exercises_done')->nullable();
            $table->text('therapist_notes')->nullable();
            $table->tinyInteger('current_pain_vas')->nullable(); // 0 - 10
            $table->text('patient_feedback')->nullable();
            $table->enum('billing_type', ['per_session', 'package_deduction'])->default('per_session');
            $table->foreignId('patient_package_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->enum('payment_status', ['paid', 'unpaid', 'package_covered'])->default('unpaid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_sessions');
    }
};
