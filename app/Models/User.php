<?php

namespace App\Models;
use App\Models\Role;
use App\Models\ParticipantDetail; 

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Traits\HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // ==============================================
    // RELASI KHUSUS BIODATA
    // ==============================================
    
    /**
     * Relasi One-to-One dengan ParticipantDetail.
     */
    public function participantDetail()
    {
        // Hubungkan model User dengan ParticipantDetail
        return $this->hasOne(ParticipantDetail::class, 'user_id');
    }

    /**
     * Relasi ke biodata umum (user_profiles).
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }
    
    /**
     * Relasi ke detail Mentor (hanya untuk role_id = 5).
     */
    public function mentorDetail(): HasOne
    {
        return $this->hasOne(MentorDetail::class);
    }

    /**
     * Relasi ke detail Karyawan/Admin/SuperAdmin (role_id 1, 2, 3).
     */
    public function employeeDetail(): HasOne
    {
        return $this->hasOne(EmployeeDetail::class);
    }


    
    // ==============================================
    // RELASI SEKOLAH (PIR-PIC)
    // ==============================================

    /**
     * Relasi ke entitas Sekolah melalui tabel pivot (untuk User role_id = 6).
     */
    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(School::class, 'school_pic_pivot', 'user_id', 'school_id')
                    ->withPivot('position') // Mengambil kolom 'position' dari tabel pivot
                    ->withTimestamps();
    }
}
