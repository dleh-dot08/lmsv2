<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Profile;
use App\Models\School; 
use App\Models\EmployeeDetail; 
use App\Models\MentorDetail;   
use App\Models\ParticipantDetail; // Model untuk detail Peserta

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // === KONSTANTA ROLE ID FINAL (SESUAI DATABASE ANDA) ===
    const ROLE_ID_SUPER_ADMIN = 1;
    const ROLE_ID_ADMIN = 2;
    const ROLE_ID_EMPLOYEE = 3;
    const ROLE_ID_PARTICIPANT = 4; // Peserta (Siswa, Mahasiswa, Umum)
    const ROLE_ID_MENTOR = 5;
    const ROLE_ID_SCHOOL_PIC = 6;
    // ====================================================

    /**
     * Menampilkan daftar semua pengguna dengan fitur search dan filter.
     */
    public function index(Request $request)
    {
        $roles = Role::where('id', '!=', self::ROLE_ID_SUPER_ADMIN)->get();
        
        $query = User::with('role', 'profile')
            // Jangan tampilkan Super Admin di daftar indeks
            ->where('role_id', '!=', self::ROLE_ID_SUPER_ADMIN)
            ->latest(); 

        // 1. FILTER BERDASARKAN ROLE ID
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // 2. FITUR PENCARIAN
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('email', 'like', $search);
            });
        }
        
        $users = $query->paginate(10)->withQueryString();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Menampilkan form untuk membuat pengguna baru.
     */
    public function create()
    {
        $roles = Role::where('id', '!=', self::ROLE_ID_SUPER_ADMIN)->get();
        $schools = School::all(); 
        
        return view('users.create', compact('roles', 'schools'));
    }

    /**
     * Menyimpan pengguna baru dan membuat baris detail kosong yang relevan.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:users'],
            'role_id'   => ['required', 'exists:roles,id'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
            
            // Validasi minimum untuk Peserta saat CREATE (untuk menentukan kategori awal)
            'participant_category' => [Rule::requiredIf($request->role_id == self::ROLE_ID_PARTICIPANT), 'nullable', 'in:siswa,mahasiswa,umum'],
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'role_id' => $validatedData['role_id'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $newRoleId = $validatedData['role_id'];

        // 1. Buat baris kosong untuk Profile (Wajib)
        $user->profile()->create(['user_id' => $user->id]);

        // 2. Buat baris kosong untuk Detail Spesifik sesuai Role
        if ($newRoleId == self::ROLE_ID_EMPLOYEE) {
            $user->employeeDetail()->create(['user_id' => $user->id]);
        } 
        
        elseif ($newRoleId == self::ROLE_ID_PARTICIPANT) {
            // Catat category awal, detail lain akan diisi NULL
            $user->participantDetail()->create([
                'user_id' => $user->id,
                'category' => $validatedData['participant_category'] ?? 'umum', // Default 'umum'
            ]);
        }

        elseif ($newRoleId == self::ROLE_ID_MENTOR) {
            $user->mentorDetail()->create(['user_id' => $user->id]);
        }
        
        return redirect()->route('users.index')->with('success', 'Pengguna baru berhasil ditambahkan! Detail dapat dilengkapi di halaman edit.');
    }

    /**
     * Menampilkan detail spesifik pengguna.
     */
    public function show(User $user)
    {
        // Muat semua relasi detail yang mungkin ada
        $user->load('role', 'profile', 'employeeDetail', 'mentorDetail', 'participantDetail', 'schools');
        
        return view('users.show', compact('user'));
    }

    /**
     * Menampilkan form untuk mengedit pengguna.
     */
    public function edit(User $user)
    {
        // Muat semua relasi detail yang mungkin ada
        $user->load('employeeDetail', 'mentorDetail', 'participantDetail', 'schools');

        $roles = Role::where('id', '!=', self::ROLE_ID_SUPER_ADMIN)->get();
        $schools = School::all(); 
        
        return view('users.edit', compact('user', 'roles', 'schools'));
    }

    /**
     * Memperbarui data pengguna, detail spesifik, dan profile.
     */
    public function update(Request $request, User $user)
    {
        // 1. VALIDASI DATA
        $rules = [
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role_id'        => ['required', 'exists:roles,id'],
            'password'       => ['nullable', 'string', 'min:8', 'confirmed'],
            
            // Validasi untuk user_profiles
            'phone_number'   => ['nullable', 'string', 'max:15'],
            'birth_place'    => ['nullable', 'string', 'max:100'],
            'birth_date'     => ['nullable', 'date'],
            'address'        => ['nullable', 'string'],
            'profile_photo'  => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            
            // Validasi detail Karyawan
            'employee_id'       => [Rule::requiredIf($request->role_id == self::ROLE_ID_EMPLOYEE), 'nullable', 'string', Rule::unique('employee_details', 'employee_id')->ignore($user->id, 'user_id')],
            'division'          => [Rule::requiredIf($request->role_id == self::ROLE_ID_EMPLOYEE), 'nullable', 'string'],
            'hire_date'         => [Rule::requiredIf($request->role_id == self::ROLE_ID_EMPLOYEE), 'nullable', 'date'],
            'emergency_contact' => [Rule::requiredIf($request->role_id == self::ROLE_ID_EMPLOYEE), 'nullable', 'string'],

            // Validasi detail Peserta
            'participant_category' => [Rule::requiredIf($request->role_id == self::ROLE_ID_PARTICIPANT), 'nullable', 'in:siswa,mahasiswa,umum'],
            'nisn' => [Rule::requiredIf($request->participant_category == 'siswa'), 'nullable', 'string'],
            'institution_name' => [Rule::requiredIf($request->role_id == self::ROLE_ID_PARTICIPANT), 'nullable', 'string', 'max:255'],
            'major' => [Rule::requiredIf($request->participant_category == 'mahasiswa'), 'nullable', 'string', 'max:255'],

            // Validasi detail Mentor
            'ktp_number'        => [Rule::requiredIf($request->role_id == self::ROLE_ID_MENTOR), 'nullable', 'string', Rule::unique('mentor_details', 'ktp_number')->ignore($user->id, 'user_id')],
            'npwp_number'       => ['nullable', 'string'],
            'bank_name'         => ['nullable', 'string'],
            'account_number'    => ['nullable', 'string'],
            'account_holder'    => ['nullable', 'string'],
            'ktp_file'          => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:5000'],
            'npwp_file'         => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:5000'],
            
            // Validasi detail PIC Sekolah
            'school_id'      => [Rule::requiredIf($request->role_id == self::ROLE_ID_SCHOOL_PIC), 'nullable', 'exists:schools,id'],
            'position'       => [Rule::requiredIf($request->role_id == self::ROLE_ID_SCHOOL_PIC), 'nullable', 'string', 'max:50'],
        ];

        $validatedData = $request->validate($rules);
        
        // Simpan Role ID LAMA untuk deteksi perubahan
        $oldRoleId = $user->role_id; 
        $newRoleId = (int) $validatedData['role_id']; 

        // DETEKSI PERUBAHAN ROLE: Di sini kita tahu apakah role diubah
        $roleChanged = ($oldRoleId != $newRoleId);
        
        // 2. UPDATE TABEL USERS (Penyimpanan data utama dan role baru)
        $user->fill([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'role_id' => $newRoleId, 
            'password' => !empty($validatedData['password']) ? Hash::make($validatedData['password']) : $user->password,
        ])->save();

        // 3. LOGIKA PENGATURAN DETAIL BERDASARKAN ROLE
        
        // --- HAPUS DETAIL LAMA JIKA ROLE BERUBAH ---
        // Jika role berubah ATAU role baru BUKAN role detail yang bersangkutan, maka detail lama dihapus
        
        if ($roleChanged || $newRoleId != self::ROLE_ID_EMPLOYEE) {
            $user->employeeDetail()->delete();
        }
        if ($roleChanged || $newRoleId != self::ROLE_ID_PARTICIPANT) {
            $user->participantDetail()->delete();
        }
        if ($roleChanged || $newRoleId != self::ROLE_ID_MENTOR) {
            // Hapus juga file-file mentor yang terkait saat detail dihapus
            if ($user->mentorDetail) {
                if ($user->mentorDetail->ktp_file_path) Storage::disk('public')->delete($user->mentorDetail->ktp_file_path);
                if ($user->mentorDetail->npwp_file_path) Storage::disk('public')->delete($user->mentorDetail->npwp_file_path);
            }
            $user->mentorDetail()->delete();
        }
        if ($roleChanged || $newRoleId != self::ROLE_ID_SCHOOL_PIC) {
             $user->schools()->detach(); // Menghapus relasi Many-to-Many
        }

        // --- BUAT/UPDATE DETAIL BARU SESUAI ROLE ---
        if ($newRoleId == self::ROLE_ID_EMPLOYEE) {
            $user->employeeDetail()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'employee_id'       => $validatedData['employee_id'],
                    'division'          => $validatedData['division'],
                    'hire_date'         => $validatedData['hire_date'],
                    'emergency_contact' => $validatedData['emergency_contact'],
                ]
            );
        } 
        
        elseif ($newRoleId == self::ROLE_ID_PARTICIPANT) {
            $user->participantDetail()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'category'         => $validatedData['participant_category'],
                    'nisn'             => $validatedData['nisn'] ?? null,
                    'institution_name' => $validatedData['institution_name'] ?? null,
                    'major'            => $validatedData['major'] ?? null,
                ]
            );
        }
        
        elseif ($newRoleId == self::ROLE_ID_MENTOR) {
            $mentorDetail = $user->mentorDetail ?? new MentorDetail(['user_id' => $user->id]);
            $ktpPath = $mentorDetail->ktp_file_path;
            $npwpPath = $mentorDetail->npwp_file_path;

            // Handle KTP file upload
            if ($request->hasFile('ktp_file')) {
                if ($ktpPath) Storage::disk('public')->delete($ktpPath);
                $ktpPath = $request->file('ktp_file')->store('mentor-ktp', 'public');
            }
            
            // Handle NPWP file upload
            if ($request->hasFile('npwp_file')) {
                if ($npwpPath) Storage::disk('public')->delete($npwpPath);
                $npwpPath = $request->file('npwp_file')->store('mentor-npwp', 'public');
            }
            
            $user->mentorDetail()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'ktp_number'      => $validatedData['ktp_number'],
                    'npwp_number'     => $validatedData['npwp_number'],
                    'bank_name'       => $validatedData['bank_name'],
                    'account_number'  => $validatedData['account_number'],
                    'account_holder'  => $validatedData['account_holder'],
                    'ktp_file_path'   => $ktpPath,
                    'npwp_file_path'  => $npwpPath,
                ]
            );
        } 
        
        elseif ($newRoleId == self::ROLE_ID_SCHOOL_PIC) {
            // Sinkronkan ke Tabel Pivot school_pic_pivot
            $user->schools()->sync([
                $validatedData['school_id'] => ['position' => $validatedData['position']]
            ]);
        }


        // 4. UPDATE/CREATE TABEL USER_PROFILES
        $profile = $user->profile ?? new Profile(['user_id' => $user->id]);

        $profileData = [
            'phone_number' => $validatedData['phone_number'],
            'birth_place'  => $validatedData['birth_place'],
            'birth_date'   => $validatedData['birth_date'],
            'address'      => $validatedData['address'],
        ];

        // 5. PEMROSESAN FOTO PROFIL
        if ($request->hasFile('profile_photo')) {
            if ($profile->profile_photo_path) {
                Storage::disk('public')->delete($profile->profile_photo_path);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $profileData['profile_photo_path'] = $path;
        }

        $profile->fill($profileData)->save();
        
        return redirect()->route('users.index')->with('success', 'Data pengguna ' . $user->name . ' berhasil diperbarui!');
    }

    /**
     * Menghapus pengguna dari database.
     */
    public function destroy(User $user)
    {
        if ($user->role_id === self::ROLE_ID_SUPER_ADMIN) {
             return redirect()->route('users.index')->with('error', 'Super Admin tidak dapat dihapus.');
        }

        // Hapus relasi Many-to-Many
        $user->schools()->detach();

        // Hapus foto profil dari storage
        if ($user->profile && $user->profile->profile_photo_path) {
            Storage::disk('public')->delete($user->profile->profile_photo_path);
        }

        $user->delete();
        
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus!');
    }
}