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
            $table->date('date_appointments');
            $table->string('start_hour');
         //   $table->foreignId('medical_office_id')->constrained('medical_offices')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('availabily_id')->constrained('availabilities')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('status');
          //  $table->unique(['date_appointments', 'medical_office_id','start_hour'], 'appt_date_medical_start_unique');
            $table->unique(['date_appointments', 'doctor_id','start_hour'], 'appt_date_medical_start_unique');
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
