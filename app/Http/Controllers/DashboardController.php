<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Penting: Import Model User untuk mengakses konstanta role ID

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama berdasarkan role pengguna.
     */
    public function index()
    {
        // 1. Dapatkan Role ID pengguna yang sedang login
        $user = Auth::user();
        $roleId = $user->role_id;
        
        // 2. Siapkan data dashboard sesuai role
        $data = $this->prepareDashboardData($roleId, $user);

        // 3. Kirim data ke view dashboard.blade.php
        return view('dashboard', $data);
    }

    /**
     * Mempersiapkan data dashboard dinamis berdasarkan Role ID.
     */
    protected function prepareDashboardData(int $roleId, User $user): array
    {
        // Variabel untuk menyimpan semua data yang akan dikirim ke view
        $dashboardData = [
            'roleId' => $roleId,
            'currentRoleName' => $this->getRoleName($roleId), // Mendapatkan nama role
            'currentMessage' => $this->getWelcomeMessage($roleId), // Mendapatkan pesan sambutan
            'userCount' => User::count(), // Realtime: Total semua pengguna
        ];

        // --- Logika Data Khusus Peran (Role ID) ---

        switch ($roleId) {
            case User::ID_SUPER_ADMIN:
            case User::ID_ADMIN:
                // Data Statis/Cadangan untuk Admin
                $dashboardData['courseCount'] = 45;
                $dashboardData['schoolCount'] = 12;
                $dashboardData['mentorCount'] = User::where('role_id', User::ID_MENTOR)->count();
                
                // Data Grafik Cadangan (Pendaftaran 4 bulan terakhir)
                $dashboardData['chartData'] = [
                    'labels' => ['Okt', 'Nov', 'Des', 'Jan'],
                    'data' => [120, 150, 90, 210],
                ];
                break;

            case User::ID_KARYAWAN:
                // Data Khusus Karyawan
                $dashboardData['taskCount'] = 8;
                $dashboardData['deadlineCount'] = 2; // Tugas mendekati deadline
                break;

            case User::ID_PESERTA:
                // Data Khusus Peserta
                // Catatan: Ini harusnya data realtime yang difilter (misalnya: Kursus::whereUserId(..) )
                $dashboardData['enrolledCourses'] = 3; // Jumlah kursus yang diikuti (Cadangan)
                $dashboardData['completedCourses'] = 1; // Jumlah kursus yang selesai (Cadangan)
                $dashboardData['progressScore'] = 78.5; // Nilai rata-rata progres (Cadangan)
                break;
                
            case User::ID_MENTOR:
                // Data Khusus Mentor
                // Catatan: Ini harusnya data realtime yang difilter berdasarkan ID Mentor
                $dashboardData['totalMaterials'] = 15; // Jumlah materi yang dibuat (Cadangan)
                $dashboardData['pendingReviews'] = 5; // Jumlah tugas/review yang menunggu (Cadangan)
                $dashboardData['ratingAverage'] = 4.8; // Rata-rata rating (Cadangan)
                break;

            case User::ID_SEKOLAH:
                // Data Khusus Sekolah
                // Catatan: Ini harusnya data realtime yang difilter berdasarkan ID Sekolah
                $dashboardData['totalStudents'] = 125; // Jumlah siswa terdaftar (Cadangan)
                $dashboardData['avgStudentScore'] = 82.1; // Rata-rata nilai siswa (Cadangan)
                break;

            default:
                // Data Default jika role ID tidak dikenali
                $dashboardData['message'] = 'Selamat datang di dashboard Anda.';
                break;
        }

        return $dashboardData;
    }

    /**
     * Mendapatkan nama peran berdasarkan ID.
     */
    protected function getRoleName(int $roleId): string
    {
        $roleNames = [
            User::ID_SUPER_ADMIN => 'Super Admin',
            User::ID_ADMIN => 'Admin',
            User::ID_KARYAWAN => 'Karyawan',
            User::ID_PESERTA => 'Peserta',
            User::ID_MENTOR => 'Mentor',
            User::ID_SEKOLAH => 'Sekolah',
        ];
        return $roleNames[$roleId] ?? 'Pengguna';
    }

    /**
     * Mendapatkan pesan sambutan berdasarkan ID.
     */
    protected function getWelcomeMessage(int $roleId): string
    {
        $roleMessages = [
            User::ID_SUPER_ADMIN => 'mengatur semua isi dan konfigurasi sistem',
            User::ID_ADMIN => 'mengelola pengguna, materi, dan konten',
            User::ID_KARYAWAN => 'mengelola tugas harian dan data internal',
            User::ID_PESERTA => 'mengikuti kursus, melihat materi, dan berinteraksi',
            User::ID_MENTOR => 'membuat, mengedit, dan mengevaluasi materi kursus',
            User::ID_SEKOLAH => 'melihat laporan kemajuan dan mengelola anggota sekolah',
        ];
        return $roleMessages[$roleId] ?? 'melanjutkan aktivitas Anda';
    }
}