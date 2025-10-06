<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkImportStaging extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     * @var string
     */
    protected $table = 'bulk_import_stagings';

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     * @var array
     */
    protected $fillable = [
        'uploaded_by', 
        'import_type', 
        'batch_token', 
        'name', 
        'email', 
        'nisn', 
        'npsn', 
        'category', 
        'major', 
        'password', 
        'initial_grade_name', 
        'target_grade_name', 
        'validation_status', 
        'validation_errors',
    ];

    /**
     * Kolom yang harus di-cast ke tipe tertentu.
     * @var array
     */
    protected $casts = [
        'validation_errors' => 'array', // Menyimpan error sebagai JSON array
    ];

    // --- RELASI (Opsional, tapi direkomendasikan) ---

    /**
     * Relasi dengan User yang mengunggah file.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}