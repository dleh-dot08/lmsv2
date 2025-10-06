<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\CourseEnrollment;
use App\Models\School; // Asumsi Model ini ada
use App\Models\Grade;  // Asumsi Model ini ada
use App\Models\Level;  // Asumsi Model ini ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;

class CourseEnrollmentController extends Controller
{
    // ID role untuk Siswa/Peserta (Ganti sesuai dengan konfigurasi database Anda)
    private const STUDENT_ROLE_ID = 4;

    /**
     * Menampilkan daftar peserta (siswa) yang terdaftar dan form pendaftaran.
     */
    public function index(Request $request, Course $course)
{
    // --- 1. Query untuk Peserta Terdaftar ($students) ---
    // PENTING: Eager load relasi melalui participantDetails agar data muncul di tabel
    $studentsQuery = $course->students()
                            ->with([
                                'participantDetails.school',
                                'participantDetails.grade',
                                'participantDetails.level' // Tambahkan level jika diperlukan
                            ]);
    
    // Fitur Pencarian Nama (untuk Peserta Terdaftar)
    if ($request->filled('search_enrolled')) {
        $studentsQuery->where('name', 'like', '%' . $request->search_enrolled . '%');
    }

    // Gunakan paginate() untuk 1000+ peserta
    $students = $studentsQuery->paginate(20, ['*'], 'enrolled_page'); 
    
    // --- 2. Query untuk Peserta Tersedia ($availableStudents) ---
    $availableStudentsQuery = User::where('role_id', self::STUDENT_ROLE_ID)
                                   ->whereDoesntHave('enrolledCourses', function ($query) use ($course) {
                                       $query->where('course_id', $course->id);
                                   });
    
    // Fitur Pencarian Nama (untuk Peserta Tersedia)
    if ($request->filled('search_available')) {
        $availableStudentsQuery->where('name', 'like', '%' . $request->search_available . '%');
    }

    // APLIKASIKAN FILTER (Dibiarkan sama, menggunakan whereHas('participantDetails', ...))
    if ($request->filled('level_id')) {
        $availableStudentsQuery->whereHas('participantDetails', fn($q) => $q->where('level_id', $request->level_id));
    }
    if ($request->filled('grade_id')) {
        $availableStudentsQuery->whereHas('participantDetails', fn($q) => $q->where('grade_id', $request->grade_id));
    }
    if ($request->filled('school_id')) {
        $availableStudentsQuery->whereHas('participantDetails', fn($q) => $q->where('school_id', $request->school_id));
    }

    // Eager load relasi school dan grade melalui participantDetails
    $availableStudents = $availableStudentsQuery
        ->with(['participantDetails.school', 'participantDetails.grade', 'participantDetails.level']) 
        ->orderBy('name')
        ->paginate(10, ['*'], 'available_page'); // <--- UBAH DARI get() MENJADI paginate(10, ...)

    
    // 3. Ambil data pendukung untuk dropdown filter
    $schools = School::orderBy('school_name')->get(); 
    $grades = Grade::orderBy('name')->get(); 
    $levels = Level::orderBy('name')->get();

    return view('courses.enrollments.index', 
        compact('course', 'students', 'availableStudents', 'schools', 'grades', 'levels')
    );
}

    /**
     * Menyimpan (mendaftarkan) satu atau lebih siswa ke dalam kursus (Bulk Enrollment).
     */
    public function store(Request $request, Course $course)
    {
        $request->validate([
            // user_ids harus berupa array karena kita menggunakan multiple select
            'user_ids' => 'required|array',
            'user_ids.*' => ['required', 'exists:users,id',
                // Rule kustom: Pastikan user belum terdaftar di course ini
                Rule::unique('course_enrollments', 'user_id')->where(function ($query) use ($course) {
                    return $query->where('course_id', $course->id);
                }),
            ],
            'status' => 'nullable|string|in:Active,Pending,Completed,Dropped',
        ], [
            'user_ids.*.unique' => 'Salah satu peserta yang Anda pilih sudah terdaftar di kelas ini.'
        ]);

        try {
            DB::transaction(function () use ($request, $course) {
                $enrollments = [];
                $status = $request->status ?? 'Active';
                
                // Siapkan data untuk disisipkan (insert)
                foreach ($request->user_ids as $userId) {
                    $enrollments[] = [
                        'user_id' => $userId,
                        'course_id' => $course->id,
                        'status' => $status,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // Menggunakan insert() massal untuk efisiensi
                CourseEnrollment::insert($enrollments);
            });

            return redirect()->route('courses.enrollments.index', $course->id)
                             ->with('success', count($request->user_ids) . ' peserta berhasil didaftarkan ke kursus.');

        } catch (\Exception $e) {
            // Log error
            return redirect()->back()->withInput()->with('error', 'Gagal mendaftarkan peserta. Mohon periksa log server.');
        }
    }

    /**
     * Memperbarui status pendaftaran (enrollment status) seorang siswa.
     */
    public function update(Request $request, Course $course, User $student)
    {
        $request->validate([
            'status' => 'required|string|in:Active,Completed,Dropped,Pending',
        ]);

        // Cari record enrollment spesifik dan update pivot
        $updatedCount = $course->students()->updateExistingPivot($student->id, [
            'status' => $request->status
        ]);
        
        if ($updatedCount > 0) {
            return redirect()->route('courses.enrollments.index', $course->id)
                             ->with('success', 'Status pendaftaran ' . $student->name . ' berhasil diperbarui menjadi ' . $request->status . '.');
        }

        return redirect()->back()->with('error', 'Gagal memperbarui status. Peserta tidak terdaftar di kursus ini.');
    }

    /**
     * Menghapus (melepaskan) pendaftaran seorang siswa dari kursus.
     */
    public function destroy(Course $course, User $student)
    {
        // Verifikasi apakah siswa benar-benar terdaftar (Logika ini sudah kita buat)
        $isEnrolled = $course->students()->where('user_id', $student->id)->exists();

        if (!$isEnrolled) {
            // Ini adalah pesan error yang Anda terima. 
            // Artinya, query 'where user_id = X' gagal menemukan record di tabel pivot.
            return redirect()->back()->with('error', 'Gagal mengeluarkan peserta. Peserta tidak terdaftar.');
        }

        // PENTING: Lakukan detach dengan ID peserta
        $detached = $course->students()->detach($student->id); 

        if ($detached) {
            return redirect()->route('courses.enrollments.index', $course->id)
                            ->with('success', 'Peserta ' . $student->name . ' berhasil dikeluarkan dari kursus.');
        }

        // Fallback jika detach berhasil tapi count-nya 0 (seharusnya tidak terjadi jika isEnrolled true)
        return redirect()->back()->with('error', 'Gagal mengeluarkan peserta. Terjadi kesalahan saat penghapusan.');
    }
}