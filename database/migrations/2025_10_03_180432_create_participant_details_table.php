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
        Schema::create('participant_details', function (Blueprint $table) {
            
            // Kolom Utama
            // user_id sebagai Kunci Utama (PRIMARY KEY) dan Kunci Asing (FOREIGN KEY)
            // Ini memastikan relasi One-to-One, di mana setiap user hanya punya satu detail peserta.
            $table->foreignId('user_id')
                  ->primary()
                  ->constrained('users') // Menghubungkan ke tabel 'users'
                  ->onDelete('cascade'); // Hapus detail jika user dihapus

            // Detail Kategori Peserta (Diisi saat 'store' di Controller)
            $table->enum('category', ['siswa', 'mahasiswa', 'umum'])
                  ->default('umum')
                  ->comment('Kategori peserta: siswa, mahasiswa, atau umum.');

            // Kolom Detail Spesifik (Semua diizinkan NULL)
            
            // Untuk Siswa
            $table->string('nisn', 255)->nullable()->unique(); 
            
            // Untuk Siswa & Mahasiswa (dan bisa juga Umum jika ada instansi)
            $table->string('institution_name', 255)->nullable()
                  ->comment('Nama Sekolah, Universitas, atau Instansi.');
            
            // Untuk Mahasiswa
            $table->string('major', 255)->nullable()
                  ->comment('Jurusan/Program Studi.');

            // Timestamp
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_details');
    }
};