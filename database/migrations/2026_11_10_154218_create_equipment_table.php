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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('equipment_code')->unique();
            $table->boolean('is_portable')->default(false);
            $table->enum('current_location', ['clinic', 'assigned_to_home_visit'])->default('clinic');
            $table->string('serial_number')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('last_serviced_date')->nullable();
            $table->date('next_service_due')->nullable();
            $table->enum('status', ['operational', 'under_maintenance', 'out_of_order'])->default('operational');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
