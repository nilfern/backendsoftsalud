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
        Schema::create('patients', function (Blueprint $table) {
              $table->id();
              $table->string('name');
              $table->string('surname');
              $table->string('dni')->unique();
              $table->string('genre');
              $table->string('photo');
              $table->string('occupation');
              $table->string('phone');
              $table->date('birthdate'); 
              $table->string('address'); 
              $table->string('email')->unique();
              $table->string('password');
              $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
              $table->rememberToken();
              $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
