<?php

namespace App\Services;

use App\Models\BulkImportStaging;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class BulkDataValidator
{
    protected $batchToken;
    protected $importType;

    public function __construct(string $batchToken, string $importType)
    {
        $this->batchToken = $batchToken;
        $this->importType = $importType;
    }

    /**
     * Rules validasi untuk tipe 'create_assign' (membuat user baru atau update detail user lama).
     */
    protected function getCreateAssignRules(): array
    {
        return [
            // Kolom Wajib untuk Create/Assign
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email', 
            'nisn' => 'required|string|max:255|unique:participant_details,nisn', 
            'npsn' => 'required|string|exists:schools,npsn', 
            
            // Kolom Opsional
            'password' => 'nullable|string|min:6',
            'category' => ['nullable', Rule::in(['siswa', 'mahasiswa', 'umum'])],
            'major' => 'nullable|string|max:255',
            'initial_grade_name' => 'nullable|string|max:100', 
            
            // Pastikan kolom untuk tipe update kosong
            'target_grade_name' => 'nullable', 
        ];
    }

    /**
     * Rules validasi untuk tipe 'grade_update' (kenaikan kelas).
     */
    protected function getGradeUpdateRules(): array
    {
        return [
            // Kolom Wajib untuk Grade Update
            'nisn' => 'required|string|exists:participant_details,nisn', 
            'target_grade_name' => 'required|string|max:100', 
            
            // Pastikan kolom untuk tipe create/assign kosong
            'name' => 'nullable', 
            'email' => 'nullable',
            'npsn' => 'nullable',
            'password' => 'nullable',
        ];
    }

    /**
     * Menjalankan proses validasi untuk semua data dalam batch token ini.
     * Updates status dan error langsung ke tabel staging.
     */
    public function validateStagingData(): void
    {
        // 1. Dapatkan semua data staging untuk batch ini
        $stagedRows = BulkImportStaging::where('batch_token', $this->batchToken)->get();
        
        // 2. Tentukan set rules yang akan digunakan
        $rules = $this->importType === 'create_assign' 
                 ? $this->getCreateAssignRules() 
                 : $this->getGradeUpdateRules();

        // Mulai transaksi untuk memastikan update status validasi berjalan lancar
        DB::transaction(function () use ($stagedRows, $rules) {
            foreach ($stagedRows as $row) {
                // Konversi model staging menjadi array untuk validasi
                $data = $row->toArray();
                
                // Gunakan Laravel Validator
                $validator = Validator::make($data, $rules);

                if ($validator->fails()) {
                    // Jika gagal, simpan status dan error (array JSON)
                    $row->update([
                        'validation_status' => 'failed',
                        'validation_errors' => $validator->errors()->all(),
                    ]);
                } else {
                    // Jika berhasil
                    $row->update([
                        'validation_status' => 'success',
                        'validation_errors' => null,
                    ]);
                }
            }
        });
    }
}
