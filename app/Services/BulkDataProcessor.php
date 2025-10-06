<?php

namespace App\Services;

use App\Models\User;
use App\Models\ParticipantDetail;
use App\Models\School;
use App\Models\Grade;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class BulkDataProcessor
{
    /**
     * Role ID Peserta Didik (asumsi 4)
     */
    const ROLE_ID_PESERTA = 4;

    /**
     * Memproses data staging yang valid (validation_status = 'success') ke tabel utama.
     * @param \Illuminate\Support\Collection $stagedData Koleksi data staging yang sudah divalidasi.
     * @return array Summary of commit results.
     */
    public function processCommit(Collection $stagedData): array
    {
        $successCount = 0;
        $failedCount = 0;
        $importType = $stagedData->first()->import_type ?? null;

        if (!$importType) {
            return ['success' => 0, 'failed' => 0];
        }

        // Transaksi mencakup seluruh batch commit
        DB::beginTransaction();
        try {
            foreach ($stagedData as $row) {
                
                if ($importType === 'create_assign') {
                    $result = $this->handleCreateAssign($row);
                } elseif ($importType === 'grade_update') {
                    $result = $this->handleGradeUpdate($row);
                } else {
                    $result = false;
                }

                if ($result) {
                    $successCount++;
                } else {
                    $failedCount++;
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Jika transaksi gagal di tengah jalan, hitung semua yang tersisa sebagai gagal
            \Log::error("Bulk commit failed for type {$importType}. Reason: " . $e->getMessage());
            return ['success' => $successCount, 'failed' => $stagedData->count() - $successCount]; 
        }

        return ['success' => $successCount, 'failed' => $failedCount];
    }
    
    /**
     * Logika untuk CREATE USER BARU & INITIAL ASSIGNMENT atau UPDATE DETAIL USER LAMA.
     */
    protected function handleCreateAssign($row): bool
    {
        try {
            $school = School::where('npsn', $row['npsn'])->first();
            if (!$school) return false;

            $participantDetail = ParticipantDetail::where('nisn', $row['nisn'])->first();
            $user = $participantDetail ? $participantDetail->user : null;

            if (!$user) {
                // --- A. CREATE USER BARU ---
                
                // 1. Tentukan Grade (Kelas) Awal
                $gradeToAssign = null;
                if ($row['initial_grade_name']) {
                    $gradeToAssign = Grade::where('level_id', $school->level_id)
                                          ->where('name', $row['initial_grade_name'])
                                          ->first();
                }
                if (!$gradeToAssign) {
                    // Ambil grade paling awal jika nama grade tidak valid/kosong
                    $gradeToAssign = Grade::where('level_id', $school->level_id)->orderBy('order', 'asc')->first();
                }
                if (!$gradeToAssign) return false;

                // 2. Buat User
                $password = $row['password'] ?? $row['nisn'] ?? 'passworddefault';
                $user = User::create([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'password' => Hash::make($password),
                    'role_id' => self::ROLE_ID_PESERTA,
                ]);

                // 3. Buat ParticipantDetail
                ParticipantDetail::create([
                    'user_id' => $user->id,
                    'category' => $row['category'] ?? 'siswa', 
                    'nisn' => $row['nisn'],
                    'institution_name' => $school->school_name,
                    'major' => $row['major'] ?? null,
                    'school_id' => $school->id,
                    'level_id' => $school->level_id,
                    'grade_id' => $gradeToAssign->id, 
                ]);
            } else {
                // --- B. UPDATE DETAIL USER LAMA (Tidak menyentuh grade_id) ---
                $updateData = [
                    'nisn' => $row['nisn'],
                    'category' => $row['category'] ?? 'siswa',
                    'institution_name' => $school->school_name,
                    'major' => $row['major'] ?? null,
                    'school_id' => $school->id,
                    'level_id' => $school->level_id,
                ];
                $user->participantDetail->update($updateData);

                // Update password jika disediakan
                if ($row['password']) {
                    $user->update(['password' => Hash::make($row['password'])]);
                }
            }
            return true;
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error("Failed to process Create/Assign for NISN {$row['nisn']}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Logika untuk BULK UPDATE GRADE (Kenaikan Kelas).
     */
    protected function handleGradeUpdate($row): bool
    {
        try {
            $detail = ParticipantDetail::where('nisn', $row['nisn'])->first();
            if (!$detail) return false;

            // Cari ID Kelas Baru (Grade)
            $newGrade = Grade::where('level_id', $detail->level_id)
                             ->where('name', $row['target_grade_name'])
                             ->first();
    
            if (!$newGrade) {
                // Grade target tidak valid untuk level siswa tersebut
                return false; 
            }

            // Lakukan UPDATE grade_id
            $detail->update([
                'grade_id' => $newGrade->id,
            ]);
            
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to process Grade Update for NISN {$row['nisn']}: " . $e->getMessage());
            return false;
        }
    }
}
