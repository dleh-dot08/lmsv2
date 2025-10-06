<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\CourseMentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseMentorController extends Controller
{
    /**
     * Menampilkan daftar mentor yang ditugaskan untuk Course tertentu.
     * * @param  \App\Models\Course  $course
     * @return \Illuminate\View\View
     */
    public function index(Course $course)
    {
        // Mengambil mentor yang ditugaskan beserta peran pivot mereka
        $mentors = $course->mentors()->get();
        
        // Mengambil daftar semua user yang memiliki role 'mentor' untuk opsi penambahan
        // Asumsi kolom 'role' ada di tabel 'users'
        // Menjadi (sesuaikan nama kolom dan nilainya)
        $availableMentors = User::where('role_id', 5) 
                                  ->orderBy('name')
                                  ->get();

        return view('courses.mentors.index', compact('course', 'mentors', 'availableMentors'));
    }

    /**
     * Menyimpan penugasan mentor baru ke kursus.
     * * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Course $course) // Menggunakan store karena form di View menggunakan POST
    {
        // 1. Validasi Input
        $request->validate([
            'mentor_utama' => 'required|exists:users,id',
            'mentor_pengganti' => 'nullable|different:mentor_utama|exists:users,id',
            'mentor_cadangan' => 'nullable|different:mentor_utama|different:mentor_pengganti|exists:users,id',
        ]);
        
        // 2. Kumpulkan ID dan Role yang valid
        $assignments = [];
        
        // Mentor Utama (WAJIB ADA)
        $assignments[$request->mentor_utama] = ['role' => 'Utama'];
        
        // Mentor Pengganti (Jika diisi dan bukan null)
        if ($request->filled('mentor_pengganti')) {
            $assignments[$request->mentor_pengganti] = ['role' => 'Pengganti'];
        }
        
        // Mentor Cadangan (Jika diisi dan bukan null)
        if ($request->filled('mentor_cadangan')) {
            $assignments[$request->mentor_cadangan] = ['role' => 'Cadangan'];
        }

        try {
            // PENTING: Gunakan SYNC untuk menimpa penugasan mentor lama dengan yang baru
            // Sync akan menghapus semua penugasan lama dan menambahkan yang baru.
            $course->mentors()->sync($assignments);
            
            return back()->with('success', 'Penugasan Mentor berhasil disinkronkan.');

        } catch (\Exception $e) {
            // Tangkap exception lain (misalnya masalah DB)
            return back()->with('error', 'Gagal menyinkronkan mentor. Pesan error: ' . $e->getMessage());
        }
    }
    
    // Metode create dan show diabaikan karena ditangani oleh index dan update

    /**
     * Mengubah peran mentor yang sudah ditugaskan.
     * * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @param  int  $mentorId ID dari User/Mentor
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Course $course, $mentorId)
    {
        $request->validate([
            'role' => 'required|in:Utama,Pengganti,Cadangan',
        ]);

        // Cek 1: Pastikan peran baru yang diubah tidak menyebabkan ada dua Mentor Utama
        if ($request->role === 'Utama' && $course->mentors()->wherePivot('role', 'Utama')->wherePivot('user_id', '!=', $mentorId)->count() > 0) {
            return back()->with('error', 'Gagal mengubah peran. Mentor Utama sudah ada. Silakan ubah peran mentor utama yang lain terlebih dahulu.');
        }

        // Cek 2: Pastikan record pivot ada
        $pivotRecord = CourseMentor::where('course_id', $course->id)
                                   ->where('user_id', $mentorId)
                                   ->first();
        if (!$pivotRecord) {
            return back()->with('error', 'Penugasan mentor tidak ditemukan.');
        }

        // Update peran menggunakan updateExistingPivot
        $course->mentors()->updateExistingPivot($mentorId, ['role' => $request->role]);

        return back()->with('success', 'Peran mentor berhasil diubah menjadi ' . $request->role . '.');
    }
    
    // Metode edit diabaikan (diganti dengan update)

    /**
     * Menghapus penugasan mentor dari kursus.
     * * @param  \App\Models\Course  $course
     * @param  int  $mentorId ID dari User/Mentor
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Course $course, $mentorId)
    {
        // Menghapus relasi menggunakan method detach
        $course->mentors()->detach($mentorId);

        return back()->with('success', 'Penugasan mentor berhasil dihapus dari kursus ini.');
    }
}