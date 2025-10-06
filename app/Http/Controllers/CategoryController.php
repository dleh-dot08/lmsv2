<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Tambahkan untuk menggunakan Str::slug

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar semua Kategori, hanya yang merupakan Kategori Induk.
     */
    public function index()
    {
        // Ambil semua kategori yang tidak memiliki induk (parent_id = NULL)
        // Dengan eager loading 'children' agar data sub-kategori juga terambil
        $categories = Category::whereNull('parent_id')
                                ->with('children')
                                ->orderBy('name', 'asc')
                                ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Menampilkan form untuk membuat Kategori baru.
     */
    public function create()
    {
        // Ambil daftar kategori induk potensial (yang parent_id-nya NULL)
        $parentCategories = Category::whereNull('parent_id')->orderBy('name', 'asc')->get();
        return view('categories.create', compact('parentCategories'));
    }

    /**
     * Menyimpan Kategori baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi Data Masukan
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'type' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id', // Pastikan parent_id ada di tabel categories
        ]);

        // 2. Menyimpan Data
        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name), // Buat slug otomatis
            'type' => $request->type,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
        ]);

        // 3. Redirect
        return redirect()->route('categories.index')
                         ->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    // Metode 'show' dihilangkan

    /**
     * Menampilkan form untuk mengedit Kategori tertentu.
     */
    public function edit(Category $category)
    {
        // Ambil daftar kategori induk potensial (kecuali kategori yang sedang diedit dan anak-anaknya)
        $parentCategories = Category::whereNull('parent_id')
                                    ->where('id', '!=', $category->id)
                                    ->orderBy('name', 'asc')
                                    ->get();

        return view('categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Memperbarui data Kategori di database.
     */
    public function update(Request $request, Category $category)
    {
        // 1. Validasi Data Masukan
        $request->validate([
            // Kecualikan ID kategori yang sedang diedit agar validasi unique tidak error
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'type' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        // 2. Memperbarui Data
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'type' => $request->type,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
        ]);

        // 3. Redirect
        return redirect()->route('categories.index')
                         ->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Menghapus Kategori dari database.
     */
    public function destroy(Category $category)
    {
        // Karena kita menggunakan onDelete('set null') di migration,
        // sub-kategori yang dimiliki akan diatur parent_id-nya menjadi NULL saat kategori ini dihapus.
        $category->delete();

        return redirect()->route('categories.index')
                         ->with('success', 'Kategori berhasil dihapus!');
    }
}