<?php

// file: app/Models/CourseMentor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CourseMentor extends Pivot
{
    /**
     * Nama tabel pivot
     */
    protected $table = 'course_mentors';

    /**
     * Atribut yang dapat diisi secara massal
     */
    protected $fillable = [
        'course_id',
        'user_id',
        'role',
    ];
    
    // Anda dapat menambahkan relasi jika diperlukan (misal: mentor() dan course())
}
