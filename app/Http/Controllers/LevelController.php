<?php

namespace App\Http\Controllers;

use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LevelController extends Controller
{
    /**
     * Menampilkan daftar Jenjang (Levels) dengan fitur search.
     */
    public function index(Request $request)
    {
        $query = Level::latest();

        // Fitur Pencarian berdasarkan nama
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where('name', 'like', $search);
        }

        $levels = $query->paginate(10)->withQueryString();

        return view('master.levels.index', compact('levels'));
    }

    /**
     * Menampilkan form untuk membuat Jenjang baru.
     */
    public function create()
    {
        return view('master.levels.create');
    }

    /**
     * Menyimpan Jenjang baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:100|unique:levels,name',
            'description' => 'nullable|string',
        ]);

        Level::create([
            'name' => $validatedData['name'],
            'slug' => Str::slug($validatedData['name']),
            'description' => $validatedData['description'],
        ]);

        return redirect()->route('levels.index')->with('success', 'Jenjang **' . $validatedData['name'] . '** berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail spesifik Jenjang.
     */
    public function show(Level $level)
    {
        // Muat relasi Grades (Kelas) yang terkait
        $level->load('grades');

        return view('master.levels.show', compact('level'));
    }

    /**
     * Menampilkan form untuk mengedit Jenjang.
     */
    public function edit(Level $level)
    {
        return view('master.levels.edit', compact('level'));
    }

    /**
     * Memperbarui Jenjang di database.
     */
    public function update(Request $request, Level $level)
    {
        $validatedData = $request->validate([
            // Pastikan nama unik kecuali untuk dirinya sendiri
            'name' => 'required|string|max:100|unique:levels,name,' . $level->id,
            'description' => 'nullable|string',
        ]);

        $level->update([
            'name' => $validatedData['name'],
            'slug' => Str::slug($validatedData['name']),
            'description' => $validatedData['description'],
        ]);

        return redirect()->route('levels.index')->with('success', 'Jenjang **' . $validatedData['name'] . '** berhasil diperbarui.');
    }

    /**
     * Menghapus Jenjang dari database.
     */
    public function destroy(Level $level)
    {
        $name = $level->name;
        
        // Catatan: Karena relasi 'onDelete('cascade')' di migrasi Grades, 
        // semua Kelas yang terhubung akan ikut terhapus otomatis.

        $level->delete();

        return redirect()->route('levels.index')->with('success', 'Jenjang **' . $name . '** dan semua Kelas di dalamnya berhasil dihapus.');
    }
}