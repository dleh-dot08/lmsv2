<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantDetail extends Model
{
    use HasFactory;
    
    // Asumsi nama tabel
    protected $table = 'participant_details'; 
    
    // Asumsi kolom-kolom detail
    protected $fillable = [
        'user_id',
        'category', // 'siswa', 'mahasiswa', 'umum'
        'nisn',     // Untuk Siswa
        'institution_name', // Nama Sekolah/Kampus/Instansi
        'major',    // Jurusan/Bidang (untuk Mahasiswa)
        // Tambahkan kolom lain jika ada
    ];

    // Relasi One-to-One
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}