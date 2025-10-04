<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_name',
        'npsn',
        'level_id', // PASTIKAN menggunakan level_id bukan school_level
        'full_address',
        'city',
        'headmaster_name',
    ];

    public function level()
    {
        // Relasi ke Model Level (Jenjang)
        return $this->belongsTo(Level::class, 'level_id');
    }

    /**
     * Relasi ke PIC Sekolah (User role_id 6) melalui tabel pivot.
     */
    public function pics(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'school_pic_pivot', 'school_id', 'user_id')
                    ->withPivot('position') 
                    ->withTimestamps();
    }

    public function students()
    {
        // Hubungkan School ke ParticipantDetail, lalu ke User
        return $this->hasMany(ParticipantDetail::class)
                    ->whereHas('user', function ($query) {
                        $query->where('role_id', User::ID_PESERTA); // Asumsi User::ID_PESERTA = 4
                    });
    }
}