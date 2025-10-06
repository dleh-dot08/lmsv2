<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleHistory extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan Model.
     * * @var string
     */
    protected $table = 'schedule_histories';

    /**
     * Tentukan apakah tabel menggunakan timestamp (created_at dan updated_at).
     * Kita hanya menggunakan 'changed_at' di migration, jadi matikan default timestamp.
     * * @var bool
     */
    public $timestamps = false;
    
    /**
     * Nama kolom timestamp yang digunakan untuk mencatat waktu perubahan.
     * Kita menggunakan 'changed_at' alih-alih 'created_at'.
     * * @var string
     */
    const CREATED_AT = 'changed_at';
    
    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     * * @var array<int, string>
     */
    protected $fillable = [
        'course_schedule_id',
        'user_id',
        'reason',
        'change_summary',
        'changed_at',
    ];

    /**
     * Casting kolom ke tipe data tertentu.
     * Kolom change_summary perlu di-cast ke array/object karena tipenya JSON di DB.
     * * @var array<string, string>
     */
    protected $casts = [
        'change_summary' => 'array',
        'changed_at' => 'datetime',
    ];

    // --- RELASI ---

    /**
     * Relasi many-to-one ke Jadwal Sesi (CourseSchedule).
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(CourseSchedule::class, 'course_schedule_id');
    }

    /**
     * Relasi many-to-one ke User (Siapa yang melakukan perubahan).
     * Asumsi Model User Anda adalah App\Models\User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}