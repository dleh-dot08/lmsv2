<?php

// app/Models/MentorDetail.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorDetail extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'ktp_number',
        'npwp_number',
        'ktp_file_path',
        'npwp_file_path',
        'bank_name',
        'account_number',
        'account_holder',
    ];

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    
    /**
     * Relasi kembali ke User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
