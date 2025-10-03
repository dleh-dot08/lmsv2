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
        Schema::create('mentor_details', function (Blueprint $table) {
            // Relasi One-to-One ke users
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->primary('user_id');
            
            // Data Legal/Pajak
            $table->string('ktp_number')->nullable()->unique();
            $table->string('npwp_number')->nullable();
            $table->string('ktp_file_path', 2048)->nullable();
            $table->string('npwp_file_path', 2048)->nullable();
            
            // Data Rekening Bank
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_holder')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentor_details');
    }
};