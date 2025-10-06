<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'academic_year',
        'is_active',
    ];

    /**
     * Konversi tipe data untuk atribut tertentu.
     */
    protected $casts = [
        'start_date' => 'date', // Laravel akan mengkonversi menjadi Carbon instance
        'end_date' => 'date',   // Laravel akan mengkonversi menjadi Carbon instance
        'is_active' => 'boolean', // Mengubah 0/1 dari DB menjadi true/false
    ];

    /**
     * Query Scope: untuk mengambil Semester yang sedang aktif.
     * Penggunaan: Semester::current()->first()
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_active', true);
    }
}
