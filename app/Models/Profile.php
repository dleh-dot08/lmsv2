<?php

// app/Models/Profile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    protected $table = 'user_profiles'; 
    protected $fillable = [
        'user_id',
        'phone_number',
        'birth_place',
        'birth_date',
        'address',
        'profile_photo_path',
    ];
    
    protected $primaryKey = 'user_id';
    public $incrementing = false; // Karena primary key adalah FK

    /**
     * Relasi kembali ke User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
