@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Manajemen Kelas /</span> Daftar Kursus & Kelas
    </h4>

    {{-- Pesan Notifikasi Sukses/Error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="m-0">Data Kursus/Kelas</h5>
        </div>

        {{-- Form Pencarian dan Filter --}}
        <div class="card-body">
            <form method="GET" action="{{ route('courses.index') }}" class="row g-3">
                
                {{-- Pencarian Nama Kelas --}}
                <div class="col-md-5">
                    <label for="search" class="form-label visually-hidden">Cari Kelas</label>
                    <input type="text" 
                        class="form-control" 
                        id="search" 
                        name="search" 
                        placeholder="Cari berdasarkan Nama Kelas..."
                        value="{{ request('search') }}">
                </div>

                {{-- Filter Semester --}}
                <div class="col-md-3">
                    <label for="semester" class="form-label visually-hidden">Filter Semester</label>
                    <select class="form-select" id="semester" name="semester">
                        <option value="">-- Semua Semester --</option>
                        {{-- Asumsi $availableSemesters dikirim dari Controller --}}
                        @if (isset($availableSemesters))
                            @foreach ($availableSemesters as $semester)
                                <option 
                                    value="{{ $semester->id }}"
                                    {{ request('semester') == $semester->id ? 'selected' : '' }}
                                >
                                    {{ $semester->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Tombol Aksi --}}
                <div class="col-md-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-secondary me-2">
                        <i class="bx bx-filter-alt me-1"></i> Filter
                    </button>
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary me-2">Reset</a>
                    <a href="{{ route('courses.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Tambah Kursus
                    </a>
                </div>
            </form>
        </div>
        
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Kelas</th>
                        <th>Kategori / Level</th>
                        <th>Mentor Utama</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th>Jadwal & Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($courses as $course)
                        @php
                            // Logika untuk menentukan status CLOSED jika waktu_akhir sudah lewat hari ini
                            $isClosed = \Carbon\Carbon::now()->greaterThan($course->waktu_akhir);
                            $displayStatus = $isClosed ? 'Closed' : $course->status;
                            $statusClass = $isClosed ? 'danger' : ($course->status == 'Aktif' ? 'success' : 'secondary');

                            // Mencari Mentor Utama
                            $mentorUtama = $course->mentors->where('pivot.role', 'Utama')->first();
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $course->nama_kelas }}</strong>
                                <br><small class="text-muted">{{ $course->grade->name ?? 'Kelas Default' }}</small>
                            </td>
                            <td>
                                {{ $course->category->name ?? '-' }}
                                <br><span class="badge bg-label-warning">{{ $course->level }}</span>
                            </td>
                            <td>
                                @if ($mentorUtama)
                                    <i class="bx bx-user-check text-primary me-1"></i> {{ $mentorUtama->name }}
                                @else
                                    <span class="text-danger">Belum Ditugaskan!</span>
                                @endif
                            </td>
                            <td>{{ $course->semester->name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-label-{{ $statusClass }} me-1">{{ $displayStatus }}</span>
                            </td>
                            <td>
                              
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('courses.show', $course->id) }}">
                                            <i class="bx bx-show-alt me-1"></i> View Detail
                                        </a>
                                        <a class="dropdown-item" href="{{ route('courses.edit', $course->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit Kelas
                                        </a>
                                        <a class="dropdown-item" href="{{ route('courses.schedules.index', $course->id) }}">
                                            <i class="bx bx-calendar me-1"></i> Atur Jadwal ({{ $course->schedules->count() ?? 0 }})
                                        </a>
                                        <a class="dropdown-item" href="{{ route('courses.mentors.index', $course->id) }}">
                                            <i class="bx bx-group me-1"></i> Atur Mentor ({{ $course->mentors->count() ?? 0 }})
                                        </a>
                                        {{-- LINK BARU: ATUR PESERTA (ENROLLMENT) --}}
                                        {{-- Asumsi Anda telah membuat relasi 'students' di Model Course --}}
                                        <a class="dropdown-item" href="{{ route('courses.enrollments.index', $course->id) }}">
                                            <i class="bx bx-user-check me-1"></i> Atur Peserta ({{ $course->students->count() ?? 0 }})
                                        </a>
                                        
                                        <div class="dropdown-divider"></div>

                                        <form action="{{ route('courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Yakin menghapus kursus {{ $course->nama_kelas }} dan semua jadwal/mentornya?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bx bx-trash me-1"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                Belum ada Kursus/Kelas yang tersedia. Silakan <a href="{{ route('courses.create') }}">tambahkan</a> yang baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Paginasi (jika Anda menggunakan paginate() di Controller) --}}
        {{-- <div class="card-footer">
            {{ $courses->links() }}
        </div> --}}
    </div>
</div>

@endsection