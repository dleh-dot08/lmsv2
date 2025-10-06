<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CourseEnrollment extends Pivot
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan Model ini.
     * @var string
     */
    protected $table = 'course_enrollments';

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'status',
    ];

    /**
     * Casting kolom ke tipe data tertentu.
     * @var array<string, string>
     */
    protected $casts = [
        // Menggunakan created_at secara implisit sebagai enrollment date
        'created_at' => 'datetime', 
    ];

    // --- RELASI ---

    /**
     * Mendapatkan Course yang terkait dengan pendaftaran ini.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Mendapatkan User (Siswa) yang terkait dengan pendaftaran ini.
     */
    public function user()
    {
        // Asumsi user_id merujuk ke model User
        return $this->belongsTo(User::class);
    }
}