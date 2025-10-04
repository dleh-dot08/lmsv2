<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GradeController extends Controller
{
    /**
     * Menampilkan daftar Kelas (Grades) dengan fitur filter berdasarkan Jenjang.
     */
    public function index(Request $request)
    {
        $levels = Level::all();
        $query = Grade::with('level')->latest('grades.updated_at'); // Ambil data Kelas terbaru

        // Filter berdasarkan level_id
        if ($request->filled('level_id')) {
            $query->where('level_id', $request->level_id);
        }
        
        // Fitur Pencarian berdasarkan nama kelas
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where('name', 'like', $search);
        }

        $grades = $query->paginate(10)->withQueryString();

        return view('master.grades.index', compact('grades', 'levels'));
    }

    /**
     * Menampilkan form untuk membuat Kelas baru.
     */
    public function create()
    {
        $levels = Level::all();
        return view('master.grades.create', compact('levels'));
    }

    /**
     * Menyimpan Kelas baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'level_id' => 'required|exists:levels,id',
            'name' => [
                'required', 
                'string', 
                'max:100',
                // Pastikan nama kelas unik di dalam jenjang yang sama
                Rule::unique('grades')->where(fn ($query) => $query->where('level_id', $request->level_id)),
            ],
            'order' => 'nullable|integer|min:0',
        ]);

        Grade::create($validatedData);

        return redirect()->route('grades.index')->with('success', 'Kelas **' . $validatedData['name'] . '** berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail spesifik Kelas.
     */
    public function show(Grade $grade)
    {
        $grade->load('level');
        return view('master.grades.show', compact('grade'));
    }

    /**
     * Menampilkan form untuk mengedit Kelas.
     */
    public function edit(Grade $grade)
    {
        $levels = Level::all();
        return view('master.grades.edit', compact('grade', 'levels'));
    }

    /**
     * Memperbarui Kelas di database.
     */
    public function update(Request $request, Grade $grade)
    {
        $validatedData = $request->validate([
            'level_id' => 'required|exists:levels,id',
            'name' => [
                'required', 
                'string', 
                'max:100',
                // Pastikan nama kelas unik di dalam jenjang yang sama, kecuali untuk dirinya sendiri
                Rule::unique('grades')->where(fn ($query) => $query->where('level_id', $request->level_id))
                    ->ignore($grade->id),
            ],
            'order' => 'nullable|integer|min:0',
        ]);

        $grade->update($validatedData);

        return redirect()->route('grades.index')->with('success', 'Kelas **' . $validatedData['name'] . '** berhasil diperbarui.');
    }

    /**
     * Menghapus Kelas dari database.
     */
    public function destroy(Grade $grade)
    {
        $name = $grade->name;
        $grade->delete();

        return redirect()->route('grades.index')->with('success', 'Kelas **' . $name . '** berhasil dihapus.');
    }
}