<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Tambahkan ini untuk menggunakan Str::slug

class ProgramController extends Controller
{
    /**
     * Menampilkan daftar semua Program. (Read)
     */
    public function index()
    {
        // Mengambil semua data program dari database
        $programs = Program::all();

        // Mengirim data ke view 'programs.index'
        return view('programs.index', compact('programs'));
    }

    /**
     * Menampilkan form untuk membuat Program baru. (Create)
     */
    public function create()
    {
        return view('programs.create');
    }

    /**
     * Menyimpan Program baru ke database. (Create - Store)
     */
    public function store(Request $request)
    {
        // 1. Validasi Data Masukan
        $request->validate([
            'name' => 'required|string|max:255|unique:programs,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // 2. Membuat Slug dari Nama Program
        $slug = Str::slug($request->name);

        // 3. Menyimpan Data ke Database
        Program::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'is_active' => $request->has('is_active'), // Cek jika checkbox dicentang
        ]);

        // 4. Redirect dengan pesan sukses
        return redirect()->route('programs.index')
                         ->with('success', 'Program baru berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail Program tertentu. (Read)
     */
    public function show(Program $program)
    {
        return view('programs.show', compact('program'));
    }

    /**
     * Menampilkan form untuk mengedit Program tertentu. (Update - Edit)
     */
    public function edit(Program $program)
    {
        return view('programs.edit', compact('program'));
    }

    /**
     * Memperbarui data Program di database. (Update - Update)
     */
    public function update(Request $request, Program $program)
    {
        // 1. Validasi Data Masukan
        $request->validate([
            // Pastikan 'name' unik, tapi kecualikan ID Program yang sedang diedit
            'name' => 'required|string|max:255|unique:programs,name,' . $program->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // 2. Membuat Slug baru jika Nama Program berubah
        $slug = Str::slug($request->name);

        // 3. Memperbarui Data di Database
        $program->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        // 4. Redirect dengan pesan sukses
        return redirect()->route('programs.index')
                         ->with('success', 'Program berhasil diperbarui!');
    }

    /**
     * Menghapus Program dari database. (Delete)
     */
    public function destroy(Program $program)
    {
        $program->delete();

        return redirect()->route('programs.index')
                         ->with('success', 'Program berhasil dihapus!');
    }
}