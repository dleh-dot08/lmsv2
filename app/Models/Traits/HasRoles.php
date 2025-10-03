<?php

// app/Models/Traits/HasRoles.php

namespace App\Models\Traits;

trait HasRoles
{
    // Konstan ID untuk peran (Role ID)
    public const ID_SUPER_ADMIN = 1;
    public const ID_ADMIN = 2;
    public const ID_KARYAWAN = 3;
    public const ID_PESERTA = 4;
    public const ID_MENTOR = 5;
    public const ID_SEKOLAH = 6;

    // Metode untuk memeriksa peran berdasarkan ID
    public function hasRole(int $roleId): bool
    {
        // $this->role_id adalah kolom di tabel users
        return $this->role_id === $roleId;
    }

    // Metode bantuan (Helper)
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::ID_SUPER_ADMIN);
    }
    
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ID_ADMIN);
    }

    public function isKaryawan(): bool
    {
        return $this->hasRole(self::ID_KARYAWAN);
    }
    
    public function isPeserta(): bool
    {
        return $this->hasRole(self::ID_PESERTA);
    }

    public function isMentor(): bool
    {
        return $this->hasRole(self::ID_MENTOR);
    }

    public function isSekolah(): bool
    {
        return $this->hasRole(self::ID_SEKOLAH);
    }
}