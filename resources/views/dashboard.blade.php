@extends('layouts.users.template') 

@section('content')

{{-- Import Model User di Blade untuk akses konstanta ID --}}
@php
    use App\Models\User;
    // Variabel seperti $roleId, $currentRoleName, $currentMessage, 
    // dan metrik lainnya sudah tersedia dari DashboardController
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        
        {{-- BLOK SAMBUTAN DAN WAKTU (Bagian atas) --}}
        <div class="col-lg-8 mb-4">
            <div class="card" style="background: url('{{ asset('assets/img/illustrations/Header.png') }}') center/cover no-repeat;">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h3 class="card-title text-primary"><b>Halo, {{ Auth::user()->name }}! &#128516; &#10024;</b></h3>
                            <p class="mb-4">
                                Anda telah login sebagai, <span class="fw-bold">{{ $currentRoleName ?? 'Pengguna' }}</span>.
                                <br>Sekarang anda bisa
                                <br>**{{ $currentMessage ?? 'melanjutkan aktivitas Anda' }}**
                            </p>
                            
                            {{-- Quick Action Button --}}
                            @if ($roleId == User::ID_ADMIN)
                                <a href="##" class="btn btn-sm btn-outline-primary">Kelola Pengguna</a>
                            @elseif ($roleId == User::ID_MENTOR)
                                <a href="##" class="btn btn-sm btn-outline-primary">Buat Materi Baru</a>
                            @elseif ($roleId == User::ID_PESERTA)
                                <a href="##" class="btn btn-sm btn-outline-primary">Lanjutkan Belajar</a>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            {{-- Ilustrasi bisa dinamis atau statis --}}
                            <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="140" alt="View Badge User" />
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
        
        {{-- BLOK WAKTU SAAT INI --}}
        <div class="col-lg-4 mb-4">
            <div class="card text-center shadow-lg p-2"
                style="border-radius: 10px; background: linear-gradient(135deg, #667eea, #24a0e7); color: #ffffff;">
                <div class="card-body">
                    <h5 class="card-title" style="color: #ffffff;">Waktu Lokal</h5>
                    <h2 id="clock" class="fw-bold" style="font-size: 48px; letter-spacing: 2px; color: #ffffff;"></h2>
                    <p id="date" style="font-size: 16px; color: #ffffff;"></p>
                </div>
            </div>
        </div>
    </div>
    
    <h5 class="fw-bold py-3 mb-4">Ringkasan Aktivitas</h5>

    {{-- ================================================================= --}}
    {{-- BLOK METRIK KHUSUS PERAN (Role Specific Metrics) --}}
    {{-- ================================================================= --}}
    
    <div class="row">
        
        {{-- METRIK ADMIN & SUPER ADMIN (Role ID 1 & 2) --}}
        @if ($roleId == User::ID_ADMIN || $roleId == User::ID_SUPER_ADMIN)
            
            {{-- Card 1: Total Pengguna (Realtime) --}}
            <div class="col-lg-3 col-md-6 mb-4">
                <x-dashboard-card icon="bx-user-plus" title="Total Pengguna" :value="number_format($userCount ?? 0)" color="primary" />
            </div>

            {{-- Card 2: Total Mentor (Realtime dari Controller) --}}
            <div class="col-lg-3 col-md-6 mb-4">
                <x-dashboard-card icon="bx-user-voice" title="Total Mentor" :value="$mentorCount ?? 0" color="success" />
            </div>

            {{-- Card 3: Total Course (Cadangan) --}}
            <div class="col-lg-3 col-md-6 mb-4">
                <x-dashboard-card icon="bx-book-content" title="Total Course" :value="$courseCount ?? 0" color="info" />
            </div>

            {{-- Card 4: Total Sekolah (Cadangan) --}}
            <div class="col-lg-3 col-md-6 mb-4">
                <x-dashboard-card icon="bx-buildings" title="Total Sekolah" :value="$schoolCount ?? 0" color="warning" />
            </div>
            
            {{-- Grafik Pendaftaran (Cadangan) --}}
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Statistik Pendaftaran 4 Bulan Terakhir (Cadangan)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="registrationChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        @endif
        
        {{-- METRIK PESERTA (Role ID 4) --}}
        @if ($roleId == User::ID_PESERTA)
            <div class="col-lg-4 col-md-6 mb-4">
                <x-dashboard-card icon="bx-chalkboard" title="Kursus Diikuti" :value="$enrolledCourses ?? 0" color="primary" />
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <x-dashboard-card icon="bx-check-circle" title="Kursus Selesai" :value="$completedCourses ?? 0" color="success" />
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <x-dashboard-card icon="bx-line-chart" title="Rata-rata Progres" :value="($progressScore ?? 0) . '%'" color="info" />
            </div>
            
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bx bx-run me-2"></i> **Ayo Segera Belajar!** Anda memiliki {{ $enrolledCourses ?? 0 }} kursus aktif.
                </div>
            </div>
        @endif

        {{-- METRIK MENTOR (Role ID 5) --}}
        @if ($roleId == User::ID_MENTOR)
            <div class="col-lg-4 col-md-6 mb-4">
                <x-dashboard-card icon="bx-layer" title="Materi Dibuat" :value="$totalMaterials ?? 0" color="warning" />
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <x-dashboard-card icon="bx-time" title="Menunggu Review" :value="$pendingReviews ?? 0" color="danger" />
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <x-dashboard-card icon="bx-star" title="Rating Rata-rata" :value="$ratingAverage ?? 0" color="success" />
            </div>
            <div class="col-12">
                <div class="alert alert-warning">
                    <i class="bx bx-bell me-2"></i> Ada **{{ $pendingReviews ?? 0 }}** review yang perlu Anda selesaikan.
                </div>
            </div>
        @endif

        {{-- METRIK SEKOLAH (Role ID 6) --}}
        @if ($roleId == User::ID_SEKOLAH)
            <div class="col-lg-6 col-md-6 mb-4">
                <x-dashboard-card icon="bx-group" title="Total Siswa Terdaftar" :value="$totalStudents ?? 0" color="primary" />
            </div>
            <div class="col-lg-6 col-md-6 mb-4">
                <x-dashboard-card icon="bx-calculator" title="Nilai Siswa Rata-rata" :value="$avgStudentScore ?? 0" color="info" />
            </div>
        @endif

        {{-- METRIK KARYAWAN (Role ID 3) --}}
        @if ($roleId == User::ID_KARYAWAN)
            <div class="col-lg-6 col-md-6 mb-4">
                <x-dashboard-card icon="bx-list-check" title="Tugas Aktif" :value="$taskCount ?? 0" color="primary" />
            </div>
            <div class="col-lg-6 col-md-6 mb-4">
                <x-dashboard-card icon="bx-calendar-exclamation" title="Mendekati Deadline" :value="$deadlineCount ?? 0" color="danger" />
            </div>
        @endif
    </div>
</div>

@endsection

{{-- ================================================================= --}}
{{-- BLOK SCRIPTS KHUSUS --}}
{{-- ================================================================= --}}
@push('scripts')
{{-- WAJIB: Import library Chart.js untuk grafik (Hanya di Admin/Super Admin) --}}
@if ($roleId == User::ID_ADMIN || $roleId == User::ID_SUPER_ADMIN)
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('registrationChart');
            
            // Data diambil dari Controller
            const chartLabels = @json($chartData['labels'] ?? []);
            const chartValues = @json($chartData['data'] ?? []);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Pendaftaran Baru',
                        data: chartValues,
                        backgroundColor: 'rgba(109, 125, 234, 0.2)',
                        borderColor: '#6d7dea',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endif


{{-- Script Clock (Waktu Saat Ini) --}}
<script>
    function updateClock() {
        var now = new Date();
        var hours = now.getHours().toString().padStart(2, '0');
        var minutes = now.getMinutes().toString().padStart(2, '0');
        var seconds = now.getSeconds().toString().padStart(2, '0');
        var timeString = hours + ':' + minutes + ':' + seconds;

        var days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        var day = days[now.getDay()];
        var month = months[now.getMonth()];
        var date = now.getDate().toString().padStart(2, '0');
        var fullDate = day + ', ' + date + ' ' + month + ' ' + now.getFullYear();

        const clockElement = document.getElementById('clock');
        const dateElement = document.getElementById('date');

        if(clockElement) clockElement.textContent = timeString;
        if(dateElement) dateElement.textContent = fullDate;
    }

    setInterval(updateClock, 1000);
    updateClock(); // Run once immediately
</script>
@endpush