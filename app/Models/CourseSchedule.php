<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'pertemuan_ke', 'tanggal_pertemuan', 
        'waktu_mulai_sesi', 'waktu_akhir_sesi', 'topik_materi', 'ruangan',
    ];

    protected $casts = [
        'tanggal_pertemuan' => 'date',
        // 'waktu_mulai_sesi' dan 'waktu_akhir_sesi' biasanya dibiarkan sebagai string/Carbon jika menggunakan date-time gabungan
    ];

    /**
     * Relasi ke Course (kelas induk)
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ScheduleHistory::class, 'course_schedule_id')->orderBy('changed_at', 'desc');
    }
}
