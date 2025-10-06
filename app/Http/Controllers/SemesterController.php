<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Digunakan untuk Transaction

class SemesterController extends Controller
{
    /**
     * Menampilkan daftar semua Semester.
     */
    public function index()
    {
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        return view('semesters.index', compact('semesters'));
    }

    /**
     * Menampilkan form untuk membuat Semester baru.
     */
    public function create()
    {
        return view('semesters.create');
    }

    /**
     * Menyimpan Semester baru ke database, dengan logika pengaktifan.
     */
    public function store(Request $request)
    {
        // 1. Validasi Data Masukan
        $request->validate([
            'name' => 'required|string|max:100|unique:semesters,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'academic_year' => 'required|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        // 2. Memulai Database Transaction
        // Ini memastikan jika ada langkah yang gagal, semua perubahan di rollback.
        DB::transaction(function () use ($request) {
            $isActive = $request->has('is_active');

            // Logika Tambahan Penting: Non-aktifkan semua semester lama jika yang baru diaktifkan
            if ($isActive) {
                Semester::where('is_active', true)->update(['is_active' => false]);
            }

            // 3. Menyimpan Data Baru
            Semester::create([
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'academic_year' => $request->academic_year,
                'is_active' => $isActive,
            ]);
        });
        
        // 4. Redirect dengan pesan sukses
        return redirect()->route('semesters.index')
                         ->with('success', 'Semester baru berhasil ditambahkan!');
    }

    // Metode 'show' dihilangkan karena biasanya tidak relevan untuk Semester

    /**
     * Menampilkan form untuk mengedit Semester tertentu.
     */
    public function edit(Semester $semester)
    {
        return view('semesters.edit', compact('semester'));
    }

    /**
     * Memperbarui data Semester di database, dengan logika pengaktifan.
     */
    public function update(Request $request, Semester $semester)
    {
        // 1. Validasi Data Masukan
        $request->validate([
            'name' => 'required|string|max:100|unique:semesters,name,' . $semester->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'academic_year' => 'required|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        // 2. Memulai Database Transaction
        DB::transaction(function () use ($request, $semester) {
            $isActive = $request->has('is_active');

            // Logika Tambahan Penting: Non-aktifkan semua semester lain jika semester ini diaktifkan
            if ($isActive && !$semester->is_active) {
                 // Hanya update jika status berubah menjadi aktif
                Semester::where('is_active', true)
                        ->where('id', '!=', $semester->id) // Jangan non-aktifkan diri sendiri
                        ->update(['is_active' => false]);
            }
            
            // 3. Memperbarui Data Semester
            $semester->update([
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'academic_year' => $request->academic_year,
                'is_active' => $isActive,
            ]);
        });

        // 4. Redirect dengan pesan sukses
        return redirect()->route('semesters.index')
                         ->with('success', 'Semester berhasil diperbarui!');
    }

    /**
     * Menghapus Semester dari database.
     */
    public function destroy(Semester $semester)
    {
        $semester->delete();

        return redirect()->route('semesters.index')
                         ->with('success', 'Semester berhasil dihapus!');
    }
}
