<?php

// app/Models/EmployeeDetail.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'division',
        'hire_date',
        'emergency_contact',
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
