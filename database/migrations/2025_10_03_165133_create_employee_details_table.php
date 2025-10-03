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
        Schema::create('employee_details', function (Blueprint $table) {
            // Relasi One-to-One ke users
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->primary('user_id');
            
            // Data Internal Karyawan
            $table->string('employee_id')->unique()->nullable(); // ID Karyawan/NIP
            $table->string('division')->nullable();
            $table->date('hire_date')->nullable();
            $table->string('emergency_contact')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_details');
    }
};