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
        Schema::create('user_profiles', function (Blueprint $table) {
            // Relasi One-to-One ke users
            // user_id sebagai kunci unik dan foreign key
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->primary('user_id'); 
            
            // Biodata Umum
            $table->string('phone_number')->nullable();
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            
            // Untuk foto profil (khusus Peserta, tapi bisa dipakai semua)
            $table->string('profile_photo_path', 2048)->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};