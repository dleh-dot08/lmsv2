<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'parent_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi: Kategori Induk (Parent).
     * Satu Kategori memiliki satu Induk.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Relasi: Kategori Anak (Children).
     * Satu Kategori Induk memiliki banyak Anak.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    
    // Anda juga bisa menambahkan relasi ke Program, jika program akan menggunakan kategori ini.
    public function programs()
    {
         return $this->hasMany(Program::class); 
    }
}