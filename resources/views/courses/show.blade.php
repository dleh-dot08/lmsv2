@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Kursus & Kelas / Detail /</span> **{{ $course->nama_kelas }}**
    </h4>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0">Informasi Umum Kursus</h5>
                    <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-edit-alt me-1"></i> Edit Data
                    </a>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Nama Kelas</dt>
                        <dd class="col-sm-9">
                            <strong>{{ $course->nama_kelas }}</strong>
                            <span class="badge bg-label-{{ $course->status == 'Aktif' ? 'success' : 'secondary' }} ms-2">{{ $course->status }}</span>
                        </dd>

                        <dt class="col-sm-3">Deskripsi</dt>
                        <dd class="col-sm-9 text-wrap">{{ $course->deskripsi ?? '—' }}</dd>

                        <dt class="col-sm-3">Tahun Ajaran</dt>
                        <dd class="col-sm-9">{{ $course->semester->name ?? 'Semester Tidak Ditemukan' }}</dd>
                        
                        <dt class="col-sm-3">Periode Kelas</dt>
                        <dd class="col-sm-9">
                            {{ $course->waktu_mulai->format('d M Y') }} s/d 
                            {{ $course->waktu_akhir->format('d M Y') }}
                            @if (\Carbon\Carbon::now()->greaterThan($course->waktu_akhir))
                                <span class="badge bg-danger ms-2">CLOSED</span>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-3">Level Kesulitan</dt>
                        <dd class="col-sm-9"><span class="badge bg-warning">{{ $course->level }}</span></dd>

                    </dl>
                </div>
            </div>

            {{-- DETAIL JADWAL PERTEMUAN --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0">Jadwal Pertemuan ({{ $course->schedules->count() }} Sesi)</h5>
                    <a href="{{ route('courses.schedules.index', $course->id) }}" class="btn btn-sm btn-outline-info">
                        <i class="bx bx-calendar me-1"></i> Kelola Jadwal
                    </a>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Waktu & Ruangan</th>
                                <th>Topik Materi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($course->schedules as $schedule)
                                <tr>
                                    <td>Pert. {{ $schedule->pertemuan_ke }}</td>
                                    <td>{{ $schedule->tanggal_pertemuan->format('D, d M Y') }}</td>
                                    <td>
                                        {{ substr($schedule->waktu_mulai_sesi, 0, 5) }} - {{ substr($schedule->waktu_akhir_sesi, 0, 5) }}
                                        <br><small class="text-muted">{{ $schedule->ruangan ?? 'Online/TBA' }}</small>
                                    </td>
                                    <td>{{ $schedule->topik_materi ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada jadwal pertemuan yang dibuat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="col-lg-4">
            {{-- SPESIFIKASI DAN PENUGASAN MENTOR --}}
            <div class="card mb-4">
                <div class="card-header"><h5 class="m-0">Klasifikasi & Penugasan Mentor</h5></div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Kategori</dt>
                        <dd class="col-sm-7">{{ $course->category->name ?? '-' }}</dd>
                        
                        <dt class="col-sm-5">Jenjang</dt>
                        <dd class="col-sm-7">{{ $course->level->name ?? '-' }}</dd>
                        
                        <dt class="col-sm-5">Kelas Fisik</dt>
                        <dd class="col-sm-7">{{ $course->grade->name ?? '-' }}</dd>
                        
                        <hr class="mt-2 mb-2">

                        <dt class="col-sm-12 mb-2">Mentor Ditugaskan ({{ $course->mentors->count() }} Orang)</dt>
                        @forelse ($course->mentors as $mentor)
                            <dt class="col-sm-5 text-muted small">{{ $mentor->pivot->role }}</dt>
                            <dd class="col-sm-7">
                                <i class="bx bx-user me-1"></i> <strong>{{ $mentor->name }}</strong>
                            </dd>
                        @empty
                            <dd class="col-sm-12 text-danger">Belum ada mentor yang ditugaskan.</dd>
                        @endforelse
                        
                        <dd class="col-sm-12 mt-3 text-end">
                            <a href="{{ route('courses.mentors.index', $course->id) }}" class="btn btn-sm btn-outline-secondary">
                                Kelola Mentor
                            </a>
                        </dd>
                    </dl>
                </div>
            </div>
            
            {{-- BLOK PESERTA KURSUS (ENROLLMENT) --}}
            <div class="card">
                <div class="card-header"><h5 class="m-0">Peserta Kursus (Enrollment)</h5></div>
                <div class="card-body">
                    
                    {{-- Logika untuk menghitung status peserta --}}
                    @php
                        $allStudents = $course->students;
                        $totalStudents = $allStudents->count();
                        // Mengelompokkan peserta berdasarkan status pivot dan menghitungnya
                        $statusCounts = $allStudents->groupBy('pivot.status')->map->count();
                    @endphp

                    <h1 class="display-5 fw-bold text-primary mb-3">{{ $totalStudents }}</h1>
                    <p class="text-muted small">Total Peserta Terdaftar</p>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <span class="badge bg-label-success w-100 py-2">Aktif: {{ $statusCounts['Active'] ?? 0 }}</span>
                        </div>
                        <div class="col-6">
                            <span class="badge bg-label-primary w-100 py-2">Selesai: {{ $statusCounts['Completed'] ?? 0 }}</span>
                        </div>
                        <div class="col-6">
                            <span class="badge bg-label-warning w-100 py-2">Menunggu: {{ $statusCounts['Pending'] ?? 0 }}</span>
                        </div>
                        <div class="col-6">
                            <span class="badge bg-label-danger w-100 py-2">Keluar: {{ $statusCounts['Dropped'] ?? 0 }}</span>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <a href="{{ route('courses.enrollments.index', $course->id) }}" class="btn btn-sm btn-outline-primary w-100">
                            <i class="bx bx-list-ul me-1"></i> Kelola Peserta
                        </a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

@endsection