<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Pastikan ini juga ada
use Carbon\Carbon; // <--- TAMBAHKAN BARIS INI!
use Illuminate\Support\Facades\DB; 
use App\Models\ScheduleHistory;

class CourseScheduleController extends Controller
{
    /**
     * Menampilkan daftar jadwal untuk Course tertentu.
     * @param  \App\Models\Course  $course
     */
    public function index(Course $course)
    {
        // Menggunakan relasi hasMany untuk mengambil jadwal
        $schedules = $course->schedules()->orderBy('pertemuan_ke', 'asc')->get();
        return view('courses.schedules.index', compact('course', 'schedules'));
    }

    /**
     * Menampilkan form untuk membuat jadwal baru.
     * @param  \App\Models\Course  $course
     */
    public function create(Course $course)
    {
        // Menghitung pertemuan ke berapa yang akan dibuat (untuk validasi maks 20)
        $nextPertemuan = $course->schedules()->count() + 1;

        if ($nextPertemuan > 20) {
             return redirect()->route('courses.schedules.index', $course->id)
                              ->with('error', 'Maksimal 20 pertemuan telah tercapai untuk kursus ini.');
        }

        return view('courses.schedules.create', compact('course', 'nextPertemuan'));
    }

    /**
     * Menyimpan jadwal baru ke database.
     */
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'pertemuan_ke' => 'required|integer|min:1|max:20|unique:course_schedules,pertemuan_ke,NULL,id,course_id,' . $course->id,
            'tanggal_pertemuan' => 'required|date|after_or_equal:' . $course->waktu_mulai . '|before_or_equal:' . $course->waktu_akhir,
            'waktu_mulai_sesi' => 'required|date_format:H:i',
            'waktu_akhir_sesi' => 'required|date_format:H:i|after:waktu_mulai_sesi',
            'topik_materi' => 'nullable|string|max:255',
            'ruangan' => 'nullable|string|max:100',
        ]);

        $course->schedules()->create($request->all());

        return redirect()->route('courses.schedules.index', $course->id)
                         ->with('success', 'Pertemuan ke-' . $request->pertemuan_ke . ' berhasil ditambahkan.');
    }
    
    // ... Metode show, edit, update, dan destroy dapat ditambahkan

    /**
     * Menampilkan form untuk mengedit jadwal pertemuan tertentu.
     * @param  \App\Models\Course  $course
     * @param  \App\Models\CourseSchedule  $schedule
     */
    public function edit(Course $course, CourseSchedule $schedule)
    {
        // Pastikan jadwal yang diedit benar-benar milik kursus ini
        if ($schedule->course_id !== $course->id) {
             abort(404);
        }

        return view('courses.schedules.edit', compact('course', 'schedule'));
    }


    /**
     * Memperbarui jadwal pertemuan di database, termasuk mencatat perubahan.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @param  \App\Models\CourseSchedule  $schedule
     */
    public function update(Request $request, Course $course, CourseSchedule $schedule)
    {
        // 1. Validasi Data
        $request->validate([
            // unique harus mengecualikan ID jadwal yang sedang diedit
            'pertemuan_ke' => 'required|integer|min:1|max:20|unique:course_schedules,pertemuan_ke,' . $schedule->id . ',id,course_id,' . $course->id,
            // Validasi tanggal harus berada dalam periode kursus
            'tanggal_pertemuan' => 'required|date|after_or_equal:' . $course->waktu_mulai->format('Y-m-d') . '|before_or_equal:' . $course->waktu_akhir->format('Y-m-d'),
            'waktu_mulai_sesi' => 'required|date_format:H:i',
            'waktu_akhir_sesi' => 'required|date_format:H:i|after:waktu_mulai_sesi',
            'topik_materi' => 'nullable|string|max:255',
            'ruangan' => 'nullable|string|max:100',
            // Field Alasan Perubahan (wajib diisi saat update)
            'alasan_perubahan' => 'required|string|min:5', 
        ]);

        // 2. Persiapan Deteksi Perubahan (untuk History)
        $changes = [];
        $originalData = $schedule->getOriginal(); // Ambil data lama dari DB
        
        // Data yang akan diupdate ke tabel course_schedules (tanpa token, method, dan alasan)
        $dataToUpdate = $request->except(['_token', '_method', 'alasan_perubahan']);
        
        $auditableFields = ['pertemuan_ke', 'tanggal_pertemuan', 'waktu_mulai_sesi', 'waktu_akhir_sesi', 'topik_materi', 'ruangan'];

        foreach ($auditableFields as $field) {
            $oldValue = $originalData[$field];
            $newValue = $dataToUpdate[$field];
            
            // Penyesuaian format nilai untuk perbandingan yang akurat
            if ($field === 'tanggal_pertemuan' && $oldValue) {
                 $oldValue = Carbon::parse($oldValue)->format('Y-m-d');
            } elseif (Str::contains($field, 'waktu_') && $oldValue) {
                 $oldValue = substr($oldValue, 0, 5); // Ambil hanya H:i
                 $newValue = substr($newValue, 0, 5); 
            }
            
            // Lakukan perbandingan
            if ($oldValue != $newValue) {
                $changes[] = [
                    'field' => $field,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                ];
            }
        }
        
        // 3. Lakukan Update Data Utama
        $schedule->update($dataToUpdate);

        // 4. Pencatatan History ke Model
        if (!empty($changes)) {
             // Simpan riwayat menggunakan Model ScheduleHistory
             ScheduleHistory::create([
                 'course_schedule_id' => $schedule->id,
                 'user_id' => auth()->id() ?? null, // Ambil ID user yang login
                 'reason' => $request->alasan_perubahan,
                 'change_summary' => $changes, // Laravel akan mengkonversi array ini ke JSON
                 'changed_at' => now(), 
             ]);
        }

        // 5. Redirect dengan pesan sukses
        return redirect()->route('courses.schedules.index', $course->id)
                         ->with('success', 'Pertemuan ke-' . $schedule->pertemuan_ke . ' berhasil diperbarui dan riwayat perubahan dicatat.');
    }

    public function destroy(Course $course, CourseSchedule $schedule)
    {
        // Pastikan jadwal yang dihapus benar-benar milik kursus ini
        if ($schedule->course_id !== $course->id) {
             abort(404);
        }

        $schedule->delete();

        return redirect()->route('courses.schedules.index', $course->id)
                         ->with('success', 'Jadwal pertemuan berhasil dihapus.');
    }

    public function history(Course $course, CourseSchedule $schedule)
    {
        // Pastikan jadwal milik kursus yang benar
        if ($schedule->course_id !== $course->id) {
            return response()->json([], 404);
        }

        // Ambil data history dengan eager load user (siapa yang mengubah)
        $histories = $schedule->histories()
                            ->with('user:id,name') // Ambil hanya ID dan Nama dari user
                            ->get();

        // Mengembalikan data dalam format JSON
        return response()->json($histories);
    }
}
