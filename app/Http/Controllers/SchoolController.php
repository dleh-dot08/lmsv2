<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SchoolController extends Controller
{
    /**
     * Menampilkan daftar semua sekolah.
     */
    public function index()
    {
        // Load sekolah dan PIC yang terhubung
        $schools = School::with('pics')->paginate(15);
        
        return view('schools.index', compact('schools'));
    }

    /**
     * Menampilkan form pembuatan sekolah baru dan menugaskan PIC.
     */
    public function create()
    {
        // Ambil semua user dengan role_id=6 yang belum terhubung ke sekolah
        $availablePics = User::where('role_id', User::ID_SEKOLAH)
                            // Anda mungkin perlu memfilter PIC yang belum memiliki sekolah
                            ->get(); 
                            
        return view('schools.create', compact('availablePics'));
    }

    /**
     * Menyimpan data sekolah baru dan menugaskan PIC.
     */
    public function store(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'npsn' => 'required|string|unique:schools,npsn|max:10',
            'headmaster_name' => 'required|string|max:255',
            'pic_user_id' => 'required|exists:users,id', // User yang akan jadi PIC
            'pic_position' => 'required|string|max:100', // Jabatan PIC
        ]);

        DB::beginTransaction();
        try {
            // 1. BUAT ENTITAS SEKOLAH
            $school = School::create($request->only([
                'school_name', 'npsn', 'school_level', 'full_address', 'city', 'headmaster_name'
            ]));

            // 2. TUGASKAN PIC SEKOLAH (Tabel Pivot)
            $school->pics()->attach($request->pic_user_id, [
                'position' => $request->pic_position,
            ]);

            DB::commit();
            return redirect()->route('schools.index')->with('success', 'Sekolah berhasil didaftarkan dan PIC ditugaskan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mendaftarkan sekolah. ' . $e->getMessage());
        }
    }

    // ... (Anda dapat menambahkan method show(), edit(), update(), destroy() di sini)
}