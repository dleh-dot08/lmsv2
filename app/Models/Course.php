<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\CourseEnrollment;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kelas', 'slug', 'deskripsi', 'category_id', 'level_id', 'grade_id', 
        'semester_id', 'level', 'status', 'waktu_mulai', 'waktu_akhir',
    ];

    protected $casts = [
        'waktu_mulai' => 'date',
        'waktu_akhir' => 'date',
    ];

    // Relasi One-to-Many (Inverse) / BelongsTo
    public function category() { return $this->belongsTo(Category::class); }
    public function level() { return $this->belongsTo(Level::class); }
    public function grade() { return $this->belongsTo(Grade::class); }
    public function semester() { return $this->belongsTo(Semester::class); }

    // Relasi One-to-Many
    public function schedules()
    {
        return $this->hasMany(CourseSchedule::class)->orderBy('pertemuan_ke');
    }

    // Relasi Many-to-Many dengan Pivot (Mentor)
    public function mentors()
    {
        // 'users' adalah tabel Mentor/User, menggunakan pivot table 'course_mentors'
        return $this->belongsToMany(User::class, 'course_mentors')
                    ->using(CourseMentor::class) // Menggunakan custom pivot model
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function students(): BelongsToMany
    {
        // Menggunakan relasi BelongsToMany dan menentukan tabel pivot
        return $this->belongsToMany(User::class, 'course_enrollments', 'course_id', 'user_id')
                    ->using(CourseEnrollment::class) // <-- GUNAKAN MODEL PIVOT BARU
                    ->withPivot('status') 
                    ->withTimestamps(); 
    }
}
