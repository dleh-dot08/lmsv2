<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\School;
use App\Models\Level;
use App\Models\Grade;
use App\Models\ParticipantDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    // Asumsi Role ID Murid/Peserta Didik dan PIC Sekolah
    const ROLE_ID_PESERTA = 4;
    const ROLE_ID_SCHOOL_PIC = 6; 

    /**
     * Menampilkan daftar semua peserta didik.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $schoolId = null;

        $query = User::where('role_id', self::ROLE_ID_PESERTA)
                     ->with('participantDetail.school', 'participantDetail.level', 'participantDetail.grade')
                     ->latest();

        // Filter: Jika user adalah PIC Sekolah, batasi hanya peserta dari sekolahnya
        if ($user->role_id == self::ROLE_ID_SCHOOL_PIC) {
             $schoolId = $user->schools->first()->id ?? null;

             if ($schoolId) {
                $query->whereHas('participantDetail', function ($q) use ($schoolId) {
                    $q->where('school_id', $schoolId);
                });
             }
        }
        
        // Filter Pencarian
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('email', 'like', $search);
            })->orWhereHas('participantDetail', function($q) use ($search) {
                $q->where('nisn', 'like', $search);
            });
        }
        
        $students = $query->paginate(20)->withQueryString();
        
         // --- DENGAN LOGIKA INI ---
            $redirectTo = $request->filled('school_id') 
                            ? route('schools.show', $request->school_id) 
                            : route('students.index');

            return redirect($redirectTo)->with('success', 'Peserta didik **' . $user->name . '** berhasil didaftarkan!');
    }

    /**
     * Menampilkan form pembuatan peserta didik baru.
     */
    public function create()
    {
        $schools = School::all();
        $levels = Level::all();
        $grades = Grade::all();
        
        return view('students.create', compact('schools', 'levels', 'grades'));
    }

    /**
     * Menyimpan data peserta didik baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            
            'nisn' => 'nullable|string|max:50|unique:participant_details,nisn',
            'school_id' => 'required|exists:schools,id',
            'level_id' => 'required|exists:levels,id',
            'grade_id' => 'required|exists:grades,id',
            'category' => 'required|string|in:siswa,mahasiswa,umum', 
            'institution_name' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
        ]);

         // --- TAMBAHAN LOGIKA PENGISIAN INSTITUTION NAME ---
        $school = School::find($request->school_id);
        if ($school) {
            $request->merge(['institution_name' => $school->school_name]);
        }

        DB::beginTransaction();
        try {
            // 1. BUAT AKUN USER
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => self::ROLE_ID_PESERTA,
            ]);

            // 2. BUAT PARTICIPANT DETAIL
            $user->participantDetail()->create($request->only([
                'nisn', 
                'school_id', 
                'level_id', 
                'grade_id', 
                'category',
                'institution_name',
                'major',
            ]));

            DB::commit();
             // --- DENGAN LOGIKA INI ---
            $redirectTo = $request->filled('school_id') 
                            ? route('schools.show', $request->school_id) 
                            : route('students.index');

            return redirect($redirectTo)->with('success', 'Peserta didik **' . $user->name . '** berhasil didaftarkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mendaftarkan peserta. Silakan coba lagi. ' . $e->getMessage());
        }
    }
    
    /**
     * Menampilkan detail peserta didik.
     */
    public function show(User $student)
    {
        if ($student->role_id != self::ROLE_ID_PESERTA) {
            abort(404);
        }
        
        $student->load('participantDetail.school', 'participantDetail.level', 'participantDetail.grade'); 
        
        return view('students.show', compact('student'));
    }

    /**
     * Menampilkan form edit peserta didik.
     */
    public function edit(User $student)
    {
        if ($student->role_id != self::ROLE_ID_PESERTA) {
            return redirect()->route('students.index')->with('error', 'Pengguna ini bukan peserta didik.');
        }

        $schools = School::all();
        $levels = Level::all();
        $grades = Grade::all();
                            
        return view('students.edit', compact('student', 'schools', 'levels', 'grades'));
    }

    /**
     * Memperbarui data peserta didik.
     */
    public function update(Request $request, User $student)
    {
        if ($student->role_id != self::ROLE_ID_PESERTA) {
            return back()->with('error', 'Pengguna ini bukan peserta didik.');
        }

        $detailId = $student->participantDetail->id ?? 0;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student->id)],
            'password' => 'nullable|string|min:6|confirmed',
            
            'nisn' => ['nullable', 'string', 'max:50', Rule::unique('participant_details', 'nisn')->ignore($detailId)],
            'school_id' => 'required|exists:schools,id',
            'level_id' => 'required|exists:levels,id',
            'grade_id' => 'required|exists:grades,id',
            'category' => 'required|string|in:siswa,mahasiswa,umum', 
            'institution_name' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $student->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->filled('password') ? Hash::make($request->password) : $student->password,
            ]);

            $student->participantDetail()->updateOrCreate(
                ['user_id' => $student->id],
                $request->only([
                    'nisn', 
                    'school_id', 
                    'level_id', 
                    'grade_id', 
                    'category',
                    'institution_name',
                    'major',
                ])
            );

            DB::commit();
             // --- DENGAN LOGIKA INI ---
            $redirectTo = $request->filled('school_id') 
                            ? route('schools.show', $request->school_id) 
                            : route('students.index');

            return redirect($redirectTo)->with('success', 'Peserta didik **' . $user->name . '** berhasil didaftarkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui peserta. Silakan coba lagi.');
        }
    }

    /**
     * Menghapus peserta didik.
     */
    public function destroy(User $student)
    {
        if ($student->role_id != self::ROLE_ID_PESERTA) {
            return back()->with('error', 'Pengguna ini bukan peserta didik.');
        }

        $studentName = $student->name;

        DB::beginTransaction();
        try {
            $student->participantDetail()->delete();
            $student->delete();

            DB::commit();
            return redirect()->route('students.index')->with('success', 'Peserta didik **' . $studentName . '** berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus peserta. Pastikan tidak ada data terkait lainnya.');
        }
    }

    /**
     * Menampilkan form penugasan sekolah ke peserta didik yang sudah ada.
     * Menggunakan LEFT JOIN untuk mencari user yang belum punya school_id.
     */
    public function assignForm(Request $request)
    {
        // Role ID Peserta Didik (asumsi CONST ROLE_ID_PESERTA = 4)
        $rolePeserta = self::ROLE_ID_PESERTA;

        // Query Perbaikan: Menggunakan LeftJoin yang lebih efisien dan andal
        // untuk mencari user yang role-nya peserta (4) DAN belum memiliki school_id.
        $unassignedStudents = User::where('users.role_id', $rolePeserta)
            ->leftJoin('participant_details', 'users.id', '=', 'participant_details.user_id')
            ->where(function($query) {
                // Kondisi 1: Belum punya row detail sama sekali (LEFT JOIN menghasilkan NULL di user_id detail)
                $query->whereNull('participant_details.user_id') 
                      // Kondisi 2: ATAU sudah punya row detail, tapi school_id masih NULL
                      ->orWhereNull('participant_details.school_id'); 
            })
            ->select('users.*') // Ambil hanya kolom dari tabel users
            ->get();
        
        // Data master
        $schools = School::with('level')->get();
        $levels = Level::all();
        $grades = Grade::all();
        
        return view('students.assign', compact('unassignedStudents', 'schools', 'levels', 'grades'));
    }

    /**
     * Menyimpan penugasan/update data sekolah peserta didik yang sudah ada.
     */

    public function assignStore(Request $request)
    {
        // Lakukan Validasi
        $request->validate([
            'user_id' => [
                'required', 
                'exists:users,id',
                Rule::in(User::where('role_id', self::ROLE_ID_PESERTA)->pluck('id')),
            ],
            
            // Data yang wajib diisi oleh form
            'school_id' => 'required|exists:schools,id', // ID Sekolah yang akan digunakan untuk redirect
            'level_id' => 'required|exists:levels,id',
            'grade_id' => 'required|exists:grades,id',
            'category' => 'required|string|in:siswa,mahasiswa,umum', 
            
            // Data yang diizinkan NULL
            'nisn' => 'nullable|string|max:50', 
            'institution_name' => 'nullable|string|max:255', 
            'major' => 'nullable|string|max:255',
        ]);
        
        // Simpan school_id untuk redirect di akhir
        $schoolId = $request->school_id;
        
        DB::beginTransaction();
        try {
            $user = User::findOrFail($request->user_id);
            $participantDetail = $user->participantDetail;
            
            // Cek darurat (walaupun kita tahu ini sudah ada)
            if (!$participantDetail) {
                DB::rollBack();
                // Jika error, redirect ke halaman assign form dengan pesan error umum
                return back()->withInput()->with('error', 'Data detail peserta tidak ditemukan. Gagal update.');
            }

            // --- 1. Pengecekan Unik NISN Manual ---
            if ($request->filled('nisn')) {
                $nisnExists = ParticipantDetail::where('nisn', $request->nisn)
                                                ->where('user_id', '!=', $user->id) 
                                                ->exists();
                if ($nisnExists) {
                    DB::rollBack();
                    return back()->withInput()->with('error', 'NISN/ID Peserta sudah digunakan oleh user lain. Silakan periksa kembali.');
                }
            }
            
            // --- 2. LAKUKAN UPDATE MURNI ---
            $participantDetail->update(
                $request->only([
                    'nisn', 
                    'school_id', 
                    'level_id', 
                    'grade_id', 
                    'category',
                    'institution_name',
                    'major',
                ])
            );

            DB::commit();
            
            // --- REDIRECT SUKSES ---
            // Mengarahkan ke route 'schools.show' menggunakan ID sekolah yang baru di-assign
            return redirect()->route('schools.show', $schoolId)
                            ->with('success', 'Sekolah berhasil ditugaskan kepada peserta **' . $user->name . '**.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // --- REDIRECT GAGAL ---
            // Jika ada error, kembali ke form dengan pesan umum.
            return back()->withInput()->with('error', 'Gagal menugaskan sekolah. Terjadi error sistem. Silakan coba lagi.');
        }
    }
}