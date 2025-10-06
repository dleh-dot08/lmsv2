<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id'; // Kunci utama adalah user_id
    public $incrementing = false; // Karena primary key bukan auto-increment
    protected $keyType = 'int';
    protected $table = 'participant_details';

    protected $fillable = [
        'user_id',
        'category',
        'nisn',
        'institution_name',
        'major',
        'school_id',
        'level_id',
        'grade_id',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    // Relasi ke School, Level, Grade
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
    
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }
    
    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }
}