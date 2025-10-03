<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Menampilkan form untuk mengedit biodata umum pengguna (user_profiles).
     */
    public function edit()
    {
        // Pastikan relasi 'profile' dimuat
        $user = Auth::user()->load('profile');

        // Jika profile belum ada, buat entri kosong
        if (!$user->profile) {
            $user->profile()->create(['user_id' => $user->id]);
            $user->load('profile'); // Muat ulang user dengan profile yang baru
        }
        
        // Anda dapat menambahkan variabel lain di sini jika diperlukan
        // Contoh: $user->mentorDetail, $user->schools, dsb.

        return view('profile.edit', compact('user'));
    }

    /**
     * Menyimpan atau memperbarui biodata umum pengguna.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // 1. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                // Pastikan email unik, kecuali jika itu email user saat ini
                Rule::unique('users')->ignore($user->id),
            ],
            // Data dari user_profiles
            'phone_number' => 'nullable|string|max:15',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            // Profile photo upload
            'profile_photo' => 'nullable|image|max:2048', // Max 2MB
        ]);

        // 2. Proses Penyimpanan Data
        DB::transaction(function () use ($request, $user) {
            
            // A. Update Data di Tabel users (Nama dan Email)
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // B. Update Data di Tabel user_profiles
            $profileData = $request->only(['phone_number', 'birth_place', 'birth_date', 'address']);

            // Proses Upload Foto Profil
            if ($request->hasFile('profile_photo')) {
                // Hapus foto lama jika ada (optional)
                if ($user->profile->profile_photo_path) {
                    // Logika penghapusan file lama di storage
                    // Storage::delete($user->profile->profile_photo_path); 
                }
                
                // Simpan foto baru
                $path = $request->file('profile_photo')->store('profile-photos', 'public');
                $profileData['profile_photo_path'] = $path;
            }

            // Gunakan updateOrCreate untuk memastikan relasi selalu ada
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id], // Kriteria pencarian
                $profileData            // Data yang akan diisi
            );
        });

        return redirect()->route('profile.edit')->with('success', 'Biodata berhasil diperbarui!');
    }

    public function show()
    {
        // Muat semua relasi yang diperlukan untuk view
        $user = Auth::user()->load('profile', 'mentorDetail', 'employeeDetail', 'schools.pics');
        
        return view('profile.view', compact('user'));
    }
}