<?php

// app/Models/School.php

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
        'school_level',
        'full_address',
        'city',
        'headmaster_name',
    ];

    /**
     * Relasi ke PIC Sekolah (User role_id 6) melalui tabel pivot.
     */
    public function pics(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'school_pic_pivot', 'school_id', 'user_id')
                    ->withPivot('position') // Mengambil jabatan PIC
                    ->withTimestamps();
    }
}
