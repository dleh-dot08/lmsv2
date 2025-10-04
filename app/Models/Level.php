<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'slug', 
        'description'
    ];

    /**
     * Relasi One-to-Many dengan Grades.
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}