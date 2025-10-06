<?php

namespace App\Imports;

use App\Models\ParticipantDetail;
use App\Models\Grade;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class GradeBulkUpdateImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        // Gunakan transaksi database untuk memastikan semua update berhasil atau tidak sama sekali
        DB::beginTransaction();
        try {
            foreach ($rows as $row) 
            {
                // Pastikan NISN dan nama kelas target ada di baris
                if (empty($row['nisn']) || empty($row['target_grade_name'])) {
                    continue; 
                }
                
                // 1. Cari peserta berdasarkan NISN
                // Karena NISN sudah divalidasi 'exists' di rules, kita bisa berasumsi detailnya ada
                $detail = ParticipantDetail::where('nisn', $row['nisn'])->first();
                
                if (!$detail) {
                    continue; // Lewati jika NISN tidak ditemukan (walaupun seharusnya dicegah oleh rules)
                }
                
                // 2. Cari ID Kelas Baru (Grade) berdasarkan Nama Kelas dan Level Siswa Saat Ini
                // Kita harus memastikan grade baru itu milik level yang sama dengan level sekolah siswa
                $newGrade = Grade::where('level_id', $detail->level_id)
                                 ->where('name', $row['target_grade_name'])
                                 ->first();
    
                if (!$newGrade) {
                    // Jika nama kelas/grade tidak valid untuk jenjang sekolah siswa, lewati
                    continue; 
                }

                // 3. Lakukan UPDATE grade_id
                $detail->update([
                    'grade_id' => $newGrade->id,
                ]);
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Penting: Throw ulang exception agar Maatwebsite/Excel bisa menangkapnya
            throw $e; 
        }
    }

    /**
     * Rules untuk validasi data per baris
     * CSV Header Wajib: nisn, target_grade_name
     */
    public function rules(): array
    {
        return [
            // NISN harus ada di CSV dan sudah terdaftar di participant_details
            '*.nisn' => 'required|string|exists:participant_details,nisn', 
            
            // Nama Kelas Target harus ada di CSV
            '*.target_grade_name' => 'required|string|max:100', 
        ];
    }

    /**
     * Custom pesan error validasi (Opsional)
     */
    public function customValidationMessages()
    {
        return [
            '*.nisn.exists' => 'NISN ini tidak ditemukan di database peserta didik.',
            '*.nisn.required' => 'Kolom NISN wajib diisi.',
            '*.target_grade_name.required' => 'Kolom Nama Kelas Target wajib diisi.',
        ];
    }
}