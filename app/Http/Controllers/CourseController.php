<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\Semester;
use App\Models\Level;
use App\Models\Grade;
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CourseController extends Controller
{
    /**
     * Menampilkan daftar semua Kursus/Kelas dengan filter dan pencarian.
     */
    public function index(Request $request)
    {
        // 1. Mulai Query dengan eager loading relasi
        $query = Course::with(['category', 'semester', 'grade', 'mentors'])
                         ->orderBy('waktu_mulai', 'desc');

        // 2. Filter Pencarian (Search)
        if ($request->filled('search')) {
            $query->where('nama_kelas', 'like', '%' . $request->search . '%');
        }

        // 3. Filter Semester
        if ($request->filled('semester')) {
            $query->where('semester_id', $request->semester);
        }
        
        // 4. Ambil Data
        $courses = $query->get(); // Gunakan get() karena Anda tidak menggunakan paginate()

        // Data untuk Filter Dropdown
        $availableSemesters = Semester::orderBy('start_date', 'desc')->get();

        return view('courses.index', compact('courses', 'availableSemesters'));
    }

    /**
     * Menampilkan form untuk membuat Kursus baru.
     */
    public function create()
    {
        // Data pendukung untuk dropdown formulir
        $categories = Category::all();
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $levels = Level::all();
        $grades = Grade::all();
        
        // Mengambil Mentor berdasarkan user_role_id = 5 (Perbaikan error)
        $mentors = User::where('role_id', 5)->orderBy('name')->get(); 

        return view('courses.create', compact('categories', 'semesters', 'levels', 'grades', 'mentors'));
    }

    /**
     * Menyimpan Kursus baru ke database, termasuk relasi Mentor.
     */
    public function store(Request $request)
    {
        // 1. Validasi Data Utama
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'semester_id' => 'required|exists:semesters,id',
            'level_id' => 'required|exists:levels,id',
            'grade_id' => 'required|exists:grades,id',
            'level' => 'required|in:Beginner,Intermediate,Advanced',
            'waktu_mulai' => 'required|date',
            'waktu_akhir' => 'required|date|after_or_equal:waktu_mulai',
            
            // Validasi Mentor ID (different memastikan ID berbeda untuk setiap peran)
            'mentor_utama' => 'required|exists:users,id',
            'mentor_pengganti' => 'nullable|exists:users,id|different:mentor_utama',
            'mentor_cadangan' => 'nullable|exists:users,id|different:mentor_utama|different:mentor_pengganti',
        ]);

        // 2. Memulai Database Transaction
        DB::transaction(function () use ($request) {
            
            // Membuat Slug unik dengan timestamp
            $slug = Str::slug($request->nama_kelas) . '-' . time();

            // Menyimpan Data Course Utama
            $course = Course::create([
                'nama_kelas' => $request->nama_kelas,
                'slug' => $slug,
                'deskripsi' => $request->deskripsi,
                'category_id' => $request->category_id,
                'semester_id' => $request->semester_id,
                'level_id' => $request->level_id,
                'grade_id' => $request->grade_id,
                'level' => $request->level,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_akhir' => $request->waktu_akhir,
                'status' => 'Aktif',
            ]);

            // 3. Menyimpan Data Mentor (Relasi Pivot)
            $mentorsData = [];
            
            $mentorsData[$request->mentor_utama] = ['role' => 'Utama'];

            if ($request->mentor_pengganti) {
                $mentorsData[$request->mentor_pengganti] = ['role' => 'Pengganti'];
            }
            
            if ($request->mentor_cadangan) {
                $mentorsData[$request->mentor_cadangan] = ['role' => 'Cadangan'];
            }
            
            $course->mentors()->attach($mentorsData);
        });

        // 4. Redirect
        return redirect()->route('courses.index')
                         ->with('success', 'Kursus/Kelas baru berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail Kursus.
     */
    public function show(Course $course)
    {
        // Eager loading semua relasi yang akan ditampilkan
        $course->load('category', 'semester', 'level', 'grade', 'mentors', 'schedules');
        return view('courses.show', compact('course'));
    }

    /**
     * Menampilkan form untuk mengedit Kursus tertentu.
     */
    public function edit(Course $course)
    {
        // Data pendukung untuk dropdown formulir
        $categories = Category::all();
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $levels = Level::all();
        $grades = Grade::all();
        
        // Mengambil Mentor berdasarkan user_role_id = 5 (Perbaikan error)
        $mentors = User::where('role_id', 5)->orderBy('name')->get(); 

        return view('courses.edit', compact('course', 'categories', 'semesters', 'levels', 'grades', 'mentors'));
    }

    /**
     * Memperbarui data Kursus di database (Termasuk Mentor).
     */
    public function update(Request $request, Course $course)
    {
        // 1. Validasi Data Utama
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'semester_id' => 'required|exists:semesters,id',
            'level_id' => 'required|exists:levels,id',
            'grade_id' => 'required|exists:grades,id',
            'level' => 'required|in:Beginner,Intermediate,Advanced',
            'status' => 'required|in:Aktif,Nonaktif,Penuh',
            'waktu_mulai' => 'required|date',
            'waktu_akhir' => 'required|date|after_or_equal:waktu_mulai',
            
            // Validasi Mentor ID
            'mentor_utama' => 'required|exists:users,id',
            'mentor_pengganti' => 'nullable|exists:users,id|different:mentor_utama',
            'mentor_cadangan' => 'nullable|exists:users,id|different:mentor_utama|different:mentor_pengganti',
        ]);

        // 2. Memulai Database Transaction
        DB::transaction(function () use ($request, $course) {
            
            // Update Data Course Utama
            $course->update([
                'nama_kelas' => $request->nama_kelas,
                'slug' => Str::slug($request->nama_kelas) . '-' . $course->id, // Regenerate slug atau biarkan jika tidak ada perubahan nama
                'deskripsi' => $request->deskripsi,
                'category_id' => $request->category_id,
                'semester_id' => $request->semester_id,
                'level_id' => $request->level_id,
                'grade_id' => $request->grade_id,
                'level' => $request->level,
                'status' => $request->status,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_akhir' => $request->waktu_akhir,
            ]);

            // 3. Sinkronisasi Data Mentor (Relasi Pivot)
            $mentorsData = [];
            $mentorsData[$request->mentor_utama] = ['role' => 'Utama'];
            
            if ($request->mentor_pengganti) {
                $mentorsData[$request->mentor_pengganti] = ['role' => 'Pengganti'];
            }
            if ($request->mentor_cadangan) {
                $mentorsData[$request->mentor_cadangan] = ['role' => 'Cadangan'];
            }
            
            // 'sync' akan menghapus relasi yang tidak ada di array baru
            $course->mentors()->sync($mentorsData);
        });

        // 4. Redirect
        return redirect()->route('courses.index')
                         ->with('success', 'Kursus/Kelas berhasil diperbarui!');
    }

    /**
     * Menghapus Kursus dari database.
     */
    public function destroy(Course $course)
    {
        // Karena kita menggunakan onDelete('cascade') di migration, 
        // schedules dan mentor pivot akan ikut terhapus otomatis.
        $course->delete(); 
        
        return redirect()->route('courses.index')->with('success', 'Kursus/Kelas berhasil dihapus!');
    }
}