<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use App\Models\Level; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SchoolController extends Controller
{
    const ROLE_ID_SCHOOL_PIC = 6; 

    public function index(Request $request)
    {

        $query = School::with('pics', 'level')
                   // TAMBAHKAN withCount untuk menghitung jumlah peserta
                   ->withCount(['students']) 
                   ->latest();

        // Hapus 'grade' dari with()
        $query = School::with('pics', 'level')->latest();
        
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where('school_name', 'like', $search)
                  ->orWhere('npsn', 'like', $search);
        }

        $schools = $query->paginate(15)->withQueryString();
        
        return view('schools.index', compact('schools'));
    }

    public function create()
    {
        $levels = Level::all();
        // Hapus Model Grade
        
        $availablePics = User::where('role_id', self::ROLE_ID_SCHOOL_PIC)
                             ->whereDoesntHave('schools')
                             ->get(); 
                            
        return view('schools.create', compact('levels', 'availablePics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'npsn' => 'required|string|unique:schools,npsn|max:10',
            'headmaster_name' => 'required|string|max:255',
            'level_id' => 'required|exists:levels,id', // Hanya Level
            'full_address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'pic_user_id' => 'nullable|exists:users,id', 
            'pic_position' => 'nullable|string|max:100', 
        ]);

        DB::beginTransaction();
        try {
            $school = School::create($request->only([
                'school_name', 
                'npsn', 
                'level_id', // Hanya Level
                'full_address', 
                'city', 
                'headmaster_name'
            ]));

            $school->pics()->attach($request->pic_user_id, [
                'position' => $request->pic_position,
            ]);

            DB::commit();
            return redirect()->route('schools.index')->with('success', 'Sekolah **' . $school->school_name . '** berhasil didaftarkan dan PIC ditugaskan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mendaftarkan sekolah. Silakan coba lagi.');
        }
    }
    
    public function show(School $school)
    {
        // Muat relasi sekolah, PIC, dan Murid.
        $school->load('pics.profile', 'level'); 
        
        // Ambil daftar peserta didik yang terhubung ke sekolah ini
        $students = User::whereHas('participantDetail', function ($query) use ($school) {
            $query->where('school_id', $school->id);
        })
        ->with('participantDetail.level', 'participantDetail.grade') // Muat detail peserta
        ->paginate(15);
        
        // Ubah view yang dipanggil
        return view('schools.show_students', compact('school', 'students'));
    }

    public function edit(School $school)
    {
        $levels = Level::all();
        // Hapus Model Grade
        
        $currentPicId = $school->pics->pluck('id');
        
        $availablePics = User::where('role_id', self::ROLE_ID_SCHOOL_PIC)
                             ->whereDoesntHave('schools')
                             ->orWhereIn('id', $currentPicId) 
                             ->get();

        $currentPic = $school->pics->first(); 
                            
        return view('schools.edit', compact('school', 'levels', 'availablePics', 'currentPic'));
    }

    public function update(Request $request, School $school)
    {
        $currentPicId = $school->pics->first()->id ?? null;
        
        $request->validate([
            'school_name' => 'required|string|max:255',
            'npsn' => ['required', 'string', 'max:10', Rule::unique('schools', 'npsn')->ignore($school->id)],
            'headmaster_name' => 'required|string|max:255',
            'level_id' => 'required|exists:levels,id', // Hanya Level
            'full_address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'pic_user_id' => ['nullable', 'exists:users,id', 
                Rule::unique('school_pic_pivot', 'user_id')->where(function ($query) use ($school) {
                    return $query->where('school_id', '!=', $school->id);
                })->ignore($currentPicId, 'user_id')
            ], 
            'pic_position' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $school->update($request->only([
                'school_name', 
                'npsn', 
                'level_id', // Hanya Level
                'full_address', 
                'city', 
                'headmaster_name'
            ]));

            $school->pics()->sync([
                $request->pic_user_id => ['position' => $request->pic_position]
            ]);

            DB::commit();
            return redirect()->route('schools.index')->with('success', 'Sekolah **' . $school->school_name . '** berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui sekolah. Silakan coba lagi.');
        }
    }

    public function destroy(School $school)
    {
        $schoolName = $school->school_name;

        DB::beginTransaction();
        try {
            $school->pics()->detach();
            $school->delete();

            DB::commit();
            return redirect()->route('schools.index')->with('success', 'Sekolah **' . $schoolName . '** berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus sekolah. Silakan coba lagi.');
        }
    }
}