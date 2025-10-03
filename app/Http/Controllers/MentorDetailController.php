<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MentorDetailController extends Controller
{
    /**
     * Menampilkan form untuk mengedit detail Mentor.
     * Hanya diakses oleh Mentor atau Admin yang mengedit profil Mentor.
     */
    public function edit(User $user)
    {
        // Asumsi: Admin bisa mengedit semua user, Mentor hanya bisa mengedit dirinya sendiri
        if (Auth::user()->role_id == User::ID_MENTOR && Auth::id() != $user->id) {
             abort(403);
        }
        
        $user->load('mentorDetail');

        // Pastikan MentorDetail sudah ada (sudah ditangani di UserController::store)
        if (!$user->mentorDetail) {
             return redirect()->back()->with('error', 'Detail mentor belum terinisialisasi. Hubungi Admin.');
        }

        return view('details.mentor.edit', compact('user'));
    }

    /**
     * Menyimpan/memperbarui detail Mentor.
     */
    public function update(Request $request, User $user)
    {
        // ... (Logika otorisasi) ...
        
        $request->validate([
            'ktp_number' => 'nullable|string|max:20',
            'npwp_number' => 'nullable|string|max:20',
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_holder' => 'required|string|max:255',
            'ktp_file' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            'npwp_file' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]);

        $data = $request->except(['ktp_file', 'npwp_file']);
        
        // Handle File Uploads
        if ($request->hasFile('ktp_file')) {
            $data['ktp_file_path'] = $request->file('ktp_file')->store('mentor-files/ktp', 'public');
            // Hapus file lama jika ada
            Storage::disk('public')->delete($user->mentorDetail->ktp_file_path ?? null);
        }

        if ($request->hasFile('npwp_file')) {
            $data['npwp_file_path'] = $request->file('npwp_file')->store('mentor-files/npwp', 'public');
            // Hapus file lama jika ada
            Storage::disk('public')->delete($user->mentorDetail->npwp_file_path ?? null);
        }

        $user->mentorDetail()->update($data);

        return redirect()->back()->with('success', 'Detail Mentor berhasil diperbarui.');
    }
}