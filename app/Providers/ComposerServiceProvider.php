<?php

namespace App\Providers; // PASTIKAN NAMESPACE INI BENAR

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ComposerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Composer untuk mengirim data ke view dashboard.blade.php
        View::composer('dashboard', function ($view) {
            $roleId = Auth::user()->role_id;

            $roleNames = [
                User::ID_SUPER_ADMIN => 'Super Admin',
                User::ID_ADMIN => 'Admin',
                User::ID_KARYAWAN => 'Karyawan',
                User::ID_PESERTA => 'Peserta',
                User::ID_MENTOR => 'Mentor',
                User::ID_SEKOLAH => 'Sekolah',
            ];

            $roleMessages = [
                User::ID_SUPER_ADMIN => 'mengatur semua isi dan konfigurasi sistem',
                User::ID_ADMIN => 'mengelola pengguna, materi, dan konten',
                // ... tambahkan semua pesan lainnya
            ];

            $view->with([
                'roleId' => $roleId,
                'currentRoleName' => $roleNames[$roleId] ?? 'Pengguna',
                'currentMessage' => $roleMessages[$roleId] ?? 'melanjutkan aktivitas Anda',
            ]);
        });
    }
    // ...
}