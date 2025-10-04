<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_id', 
        'name', 
        'order'
    ];

    /**
     * Relasi Many-to-One dengan Level.
     */
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    // Relasi ke peserta didik
    public function students()
    {
        return $this->hasMany(User::class, 'grade_id');
    }
}