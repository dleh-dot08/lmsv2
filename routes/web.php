<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\MentorDetailController;
use App\Http\Controllers\EmployeeDetailController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    
    // Semua pengguna yang login diarahkan ke DashboardController
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile/view', [ProfileController::class, 'show'])->name('profile.view');
    
    // ... rute-rute role-spesifik lainnya
});

// routes/web.php

// Pastikan semua Controller di-import di bagian atas file

Route::middleware(['auth', 'role:2|1'])->group(function () {
    
    // --- MANAJEMEN PENGGUNA (CRUD) ---
    Route::resource('users', UserController::class);

    // --- MANAJEMEN MASTER SEKOLAH ---
    Route::resource('schools', SchoolController::class);

    // --- MANAJEMEN DETAIL KHUSUS (DIEDIT OLEH ADMIN/USER) ---
    // Rute ini bisa diletakkan di luar grup admin jika user diizinkan mengedit dirinya sendiri
    Route::prefix('details/{user}')->group(function () {
        // Mentor
        Route::get('mentor', [MentorDetailController::class, 'edit'])->name('details.mentor.edit');
        Route::put('mentor', [MentorDetailController::class, 'update'])->name('details.mentor.update');

        // Karyawan/Admin
        Route::get('employee', [EmployeeDetailController::class, 'edit'])->name('details.employee.edit');
        Route::put('employee', [EmployeeDetailController::class, 'update'])->name('details.employee.update');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
