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
        // Tabel A: Entitas Sekolah (Master Data)
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('school_name');
            $table->string('npsn')->unique(); // Wajib unik
            $table->string('school_level')->nullable(); // SD, SMP, SMA, etc.
            $table->text('full_address')->nullable();
            $table->string('city')->nullable();
            $table->string('headmaster_name')->nullable(); // Nama Kepala Sekolah
            $table->timestamps();
        });

        // Tabel B: Pivot/Relasi untuk PIC Sekolah (Role ID 6)
        Schema::create('school_pic_pivot', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // PIC (User Role 6)
            $table->foreignId('school_id')->constrained()->onDelete('cascade'); // Entitas Sekolah
            
            $table->string('position')->nullable(); // Jabatan PIC (Guru, Admin, Kaprog)

            // Menjadikan kombinasi keduanya Primary Key
            $table->primary(['user_id', 'school_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_pic_pivot');
        Schema::dropIfExists('schools');
    }
};