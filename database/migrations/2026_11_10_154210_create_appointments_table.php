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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_no')->unique()->index();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('therapist_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('service_location', ['clinic', 'home_visit'])->default('clinic');
            $table->text('home_address')->nullable();
            $table->string('home_landmark')->nullable();
            $table->decimal('travel_distance_km', 6, 2)->default(0.00);
            $table->decimal('travel_charge', 10, 2)->default(0.00);
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('travel_buffer_minutes')->default(0);
            $table->enum('status', ['scheduled', 'checked_in', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->integer('token_number')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
