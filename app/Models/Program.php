<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     * Ini adalah kolom-kolom yang boleh diisi melalui request.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    /**
     * Konversi tipe data untuk atribut tertentu.
     */
    protected $casts = [
        'is_active' => 'boolean', // Mengubah 0/1 dari DB menjadi true/false di PHP
    ];

    /**
     * Mendefinisikan relasi dengan User (jika User bisa memiliki banyak Program)
     * Kita asumsikan ada tabel 'program_user' (pivot table).
     */
    public function users()
    {
         return $this->belongsToMany(User::class);
    }
}