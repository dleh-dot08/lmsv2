<?php

namespace App\Imports;

use App\Models\User;
use App\Models\School;
use App\Models\ParticipantDetail;
use App\Models\Grade;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsBulkImport implements ToCollection, WithHeadingRow, WithValidation
{
    // Konstanta untuk Role ID Peserta Didik (Sesuaikan jika di sistem Anda berbeda)
    const ROLE_ID_PESERTA = 4;
    
    public function collection(Collection $rows)
    {
        // Jalankan import dalam Transaction untuk memastikan semua berhasil atau semua gagal
        \DB::beginTransaction();
        try {
            foreach ($rows as $row) 
            {
                // Pastikan data penting ada sebelum diproses lebih lanjut
                if (empty($row['nisn']) || empty($row['npsn']) || empty($row['email'])) {
                    continue; // Lewati baris yang datanya sangat kurang
                }
                
                // 1. Dapatkan data Sekolah (Wajib ada)
                $school = School::where('npsn', $row['npsn'])->first();
                if (!$school) {
                    // Ini seharusnya sudah ditangkap oleh rules, tapi ini untuk safety
                    continue; 
                }
                
                // 2. Tentukan Grade (Kelas) untuk user baru
                $gradeToAssign = null;
    
                if (isset($row['initial_grade_name']) && $row['initial_grade_name']) {
                    // Coba cari berdasarkan Nama Kelas di CSV untuk jenjang sekolah ini
                    $gradeToAssign = Grade::where('level_id', $school->level_id)
                                          ->where('name', $row['initial_grade_name'])
                                          ->first();
                }
                
                if (!$gradeToAssign) {
                    // Jika tidak ada di CSV atau tidak ditemukan, ambil Kelas Paling Awal (default)
                    $gradeToAssign = Grade::where('level_id', $school->level_id)
                                          ->orderBy('order', 'asc')
                                          ->first();
                }
    
                // 3. Cek apakah user sudah ada (berdasarkan NISN)
                $participantDetail = ParticipantDetail::where('nisn', $row['nisn'])->first();
                $user = $participantDetail ? $participantDetail->user : null;
    
                if (!$user) {
                    // --- A. CREATE USER BARU & INITIAL ASSIGNMENT ---
                    
                    if (!$gradeToAssign) { continue; } // Gagal buat user jika kelas awal tidak ditemukan
    
                    // Tentukan password: pakai kolom CSV, jika kosong pakai NISN (atau default keras)
                    $password = $row['password'] ?? $row['nisn'] ?? 'passworddefault';
    
                    $user = User::create([
                        'name' => $row['name'],
                        'email' => $row['email'],
                        'password' => Hash::make($password),
                        'role_id' => self::ROLE_ID_PESERTA,
                    ]);
    
                    ParticipantDetail::create([
                        'user_id' => $user->id,
                        'category' => $row['category'] ?? 'siswa', 
                        'nisn' => $row['nisn'],
                        'institution_name' => $school->school_name,
                        'major' => $row['major'] ?? null,
                        
                        // PENETAPAN SEKOLAH & KELAS AWAL
                        'school_id' => $school->id,
                        'level_id' => $school->level_id,
                        'grade_id' => $gradeToAssign->id, // Kelas Awal
                    ]);
                } else {
                    // --- B. UPDATE DETAIL USER LAMA ---
                    // Hanya perbarui detail sekolah, TAPI JANGAN SENTUH grade_id.
                    $user->participantDetail->update([
                        'nisn' => $row['nisn'],
                        'category' => $row['category'] ?? 'siswa',
                        'institution_name' => $school->school_name,
                        'major' => $row['major'] ?? null,
                        'school_id' => $school->id,
                        'level_id' => $school->level_id,
                        // grade_id TIDAK di-update di sini.
                    ]);
                    
                    // Jika password di CSV diisi, update juga password user
                    if (isset($row['password']) && $row['password']) {
                         $user->update(['password' => Hash::make($row['password'])]);
                    }
                }
            }
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            // Penting: Throw ulang exception agar Maatwebsite/Excel bisa menangkapnya
            throw $e; 
        }
    }

    /**
     * Rules untuk validasi data per baris
     */
    public function rules(): array
    {
        return [
            // RULES WAJIB
            '*.name' => 'required|string|max:255',
            '*.email' => 'required|email|unique:users,email', 
            '*.nisn' => 'required|string|max:255|unique:participant_details,nisn', 
            '*.npsn' => 'required|string|exists:schools,npsn', 
            
            // RULES OPSIONAL
            '*.password' => 'nullable|string|min:6',
            '*.category' => ['nullable', Rule::in(['siswa', 'mahasiswa', 'umum'])],
            '*.major' => 'nullable|string|max:255',
            '*.initial_grade_name' => 'nullable|string|max:100', 
        ];
    }

    /**
     * Jika Anda ingin custom pesan error (Opsional)
     */
    public function customValidationMessages()
    {
        return [
            '*.email.unique' => 'Email ini sudah terdaftar. Harap gunakan email yang berbeda.',
            '*.nisn.unique' => 'NISN ini sudah terdaftar. Data lama harus di-update, bukan di-create ulang.',
            '*.npsn.exists' => 'NPSN yang dimasukkan tidak ditemukan di database Sekolah.',
        ];
    }
}