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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            
            // Kunci Asing ke tabel Jenjang (levels)
            $table->foreignId('level_id')
                  ->constrained('levels')
                  ->onDelete('cascade')
                  ->comment('Relasi ke tabel levels (SD, SMP, dll.)');
            
            $table->string('name', 100)->comment('Nama Kelas/Tingkatan, contoh: Kelas 1, Kelas 7, Semester 1');
            $table->integer('order')->default(0)->comment('Urutan untuk sorting (misal: Kelas 1 = 1, Kelas 2 = 2)');
            $table->timestamps();

            // Tambahkan unique constraint agar tidak ada Kelas yang sama dalam Jenjang yang sama
            $table->unique(['level_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};