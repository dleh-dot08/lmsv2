<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';

      public $incrementing = false; 
    
    protected $table = 'participant_details'; 
    
    protected $fillable = [
        'user_id',
        'category',
        'nisn', // ID Peserta (Siswa/Mahasiswa)
        'institution_name', 
        'major',
        // KOLOM BARU YANG DITAMBAHKAN
        'school_id',
        'level_id',
        'grade_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // RELASI BARU
    public function school()
    {
        return $this->belongsTo(School::class);
    }
    
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
}