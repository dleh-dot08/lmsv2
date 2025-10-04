<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participant_details', function (Blueprint $table) {
            // Kolom sudah ada di Model Anda: $table->string('nisn')->nullable();

            // Tambahkan relasi ke Sekolah
            $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('set null')->after('nisn');
            
            // Tambahkan relasi ke Jenjang
            $table->foreignId('level_id')->nullable()->constrained('levels')->onDelete('set null');
            
            // Tambahkan relasi ke Kelas/Tingkatan
            $table->foreignId('grade_id')->nullable()->constrained('grades')->onDelete('set null');
            
            // Kolom identitas murid (ganti 'nisn' menjadi 'student_id_number' jika Anda ingin lebih umum)
            // Karena Model Anda sudah punya 'nisn', kita gunakan itu, tapi kita bisa tambah 'nim' jika perlu.
            // Untuk kesederhanaan, mari kita asumsikan 'nisn' adalah ID unik peserta.
        });
    }

    public function down(): void
    {
        Schema::table('participant_details', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropForeign(['level_id']);
            $table->dropForeign(['grade_id']);
            $table->dropColumn(['school_id', 'level_id', 'grade_id']);
            
            // Jika Anda menambahkan kolom lain seperti 'nim' di sini, hapus juga.
        });
    }
};