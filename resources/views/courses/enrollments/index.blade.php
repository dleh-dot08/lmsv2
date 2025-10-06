@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Kelas / {{ $course->nama_kelas }} /</span> Kelola Peserta (Enrollment)
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

    {{-- ======================================================= --}}
    {{-- BLOK 1: DAFTARKAN PESERTA BARU (Search, Filter, dan Tabel) --}}
    {{-- ======================================================= --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="m-0">1. Daftarkan Peserta Baru ke Kelas Ini</h5>
            <small class="text-muted">Gunakan filter di bawah untuk menemukan calon peserta. ({{ $availableStudents->total() }} Ditemukan)</small>
        </div>
        
        <div class="card-body pb-0">
            {{-- Form Filter dan Search --}}
            <form method="GET" action="{{ route('courses.enrollments.index', $course->id) }}" class="mb-4 border-bottom pb-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Cari Nama</label>
                        <input type="search" name="search_available" class="form-control form-control-sm" placeholder="Nama Peserta Tersedia..." value="{{ request('search_available') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jenjang/Level</label>
                        <select class="form-select form-select-sm" name="level_id">
                            <option value="">-- Semua Jenjang --</option>
                            @foreach ($levels as $level)
                                <option value="{{ $level->id }}" {{ request('level_id') == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kelas/Grade</label>
                        <select class="form-select form-select-sm" name="grade_id">
                            <option value="">-- Semua Kelas --</option>
                            @foreach ($grades as $grade)
                                <option value="{{ $grade->id }}" {{ request('grade_id') == $grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- FILTER SEKOLAH YANG HILANG --}}
                    <div class="col-md-3">
                        <label class="form-label">Sekolah</label>
                        <select class="form-select form-select-sm" name="school_id">
                            <option value="">-- Semua Sekolah --</option>
                            @foreach ($schools as $school)
                                <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>{{ $school->school_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Tombol Filter dan Reset --}}
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-sm btn-primary me-2">Cari & Filter</button>
                         @if(request()->hasAny(['level_id', 'grade_id', 'school_id', 'search_available']))
                            <a href="{{ route('courses.enrollments.index', $course->id) }}" class="btn btn-sm btn-outline-secondary">Reset Filter</a>
                        @endif
                    </div>
                </div>
            </form>
            
            {{-- Form Enrollment (Bulk Invite) --}}
            <form method="POST" action="{{ route('courses.enrollments.store', $course->id) }}">
                @csrf

                <h6 class="mb-3">Hasil Pencarian Peserta (Halaman {{ $availableStudents->currentPage() }})</h6>
                
                @if ($availableStudents->isEmpty() && request()->hasAny(['level_id', 'grade_id', 'school_id', 'search_available']))
                    <div class="alert alert-info">
                        Tidak ada peserta yang cocok dengan kriteria pencarian/filter.
                    </div>
                @elseif ($availableStudents->isEmpty())
                    <div class="alert alert-warning">
                        Tidak ada peserta baru yang tersedia untuk kelas ini.
                    </div>
                @else
                    <div class="table-responsive text-nowrap mb-3" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">
                                        <input type="checkbox" id="checkAll">
                                    </th>
                                    <th>Nama Peserta</th>
                                    <th>Sekolah</th>
                                    <th>Kelas/Jenjang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($availableStudents as $student)
                                    @php
                                        // Mengakses relasi melalui participantDetails
                                        $details = $student->participantDetails;
                                        $schoolName = $details->school->school_name ?? 'N/A';
                                        $gradeName = $details->grade->name ?? 'N/A';
                                        $levelName = $details->level->name ?? 'N/A';
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="user_ids[]" value="{{ $student->id }}" class="checkItem">
                                        </td>
                                        <td>{{ $student->name }}</td>
                                        <td>{{ $schoolName }}</td>
                                        <td>{{ $gradeName }} ({{ $levelName }})</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination untuk Peserta Tersedia --}}
                    <div class="d-flex justify-content-end mb-3">
                         {{-- Appends request kecuali halaman peserta aktif ('enrolled_page') --}}
                         {{ $availableStudents->appends(request()->except('available_page', 'enrolled_page'))->links() }}
                    </div>

                    {{-- Status Awal & Tombol Daftar --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status Awal Pendaftaran</label>
                            <select class="form-select" id="status" name="status">
                                <option value="Active" selected>Aktif</option>
                                <option value="Pending">Menunggu</option>
                            </select>
                        </div>
                        <div class="col-md-9 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100" id="btnEnroll" disabled>
                                Daftarkan Peserta yang Dipilih
                            </button>
                        </div>
                    </div>

                @endif
                
                @error('user_ids')
                    <div class="alert alert-danger d-block">{{ $message }}</div>
                @enderror
                @error('user_ids.*')
                    <div class="alert alert-danger d-block">{{ $message }}</div>
                @enderror
            </form>
        </div>
    </div>
    
    {{-- =================================== --}}
    {{-- BLOK 3: TABEL PESERTA AKTIF KELAS --}}
    {{-- =================================== --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0">2. Peserta Aktif Kelas ({{ $students->total() }} Orang)</h5>
            
            {{-- Form Pencarian Peserta Terdaftar --}}
            <form method="GET" action="{{ route('courses.enrollments.index', $course->id) }}" class="d-flex" style="width: 250px;">
                <input type="search" name="search_enrolled" class="form-control form-control-sm me-2" placeholder="Cari Nama Peserta..." value="{{ request('search_enrolled') }}">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bx bx-search"></i></button>
            </form>
        </div>
        
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Peserta</th>
                        <th>Sekolah & Kelas</th>
                        <th>Tanggal Daftar</th>
                        <th>Status & Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($students as $student)
                        @php
                            // Mengakses relasi melalui participantDetails
                            $details = $student->participantDetails;
                            $schoolName = $details->school->school_name ?? 'N/A';
                            $gradeName = $details->grade->name ?? 'N/A';
                            
                            $status = $student->pivot->status;
                            $badgeColor = [
                                'Active' => 'success',
                                'Completed' => 'primary',
                                'Dropped' => 'danger',
                                'Pending' => 'warning',
                            ][$status] ?? 'secondary';
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}</td>
                            <td>
                                <strong>{{ $student->name }}</strong>
                            </td>
                            <td>
                                {{ $schoolName }}
                                <small class="text-muted d-block">Kelas: {{ $gradeName }}</small>
                            </td>
                            <td>
                                {{ $student->pivot->created_at->format('d M Y') }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    {{-- Form Update Status --}}
                                    <form method="POST" action="{{ route('courses.enrollments.update', [$course->id, $student->id]) }}">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                                            <option value="Active" {{ $status == 'Active' ? 'selected' : '' }}>Aktif</option>
                                            <option value="Completed" {{ $status == 'Completed' ? 'selected' : '' }}>Selesai</option>
                                            <option value="Dropped" {{ $status == 'Dropped' ? 'selected' : '' }}>Keluar</option>
                                            <option value="Pending" {{ $status == 'Pending' ? 'selected' : '' }}>Menunggu</option>
                                        </select>
                                    </form>
                                    
                                    {{-- Form Hapus (Detach) --}}
                                    <form action="{{ route('courses.enrollments.destroy', [$course->id, $student->id]) }}" method="POST" onsubmit="return confirm('Yakin mengeluarkan peserta {{ $student->name }} dari kursus?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Keluarkan Peserta">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bx bx-user-x d-block mb-1 fs-3"></i>
                                Belum ada peserta yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Link Pagination --}}
        <div class="card-footer">
            {{ $students->appends(request()->except('enrolled_page'))->links() }}
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('courses.show', $course->id) }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali ke Detail Kursus
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkAll = document.getElementById('checkAll');
        const checkItems = document.querySelectorAll('.checkItem');
        const btnEnroll = document.getElementById('btnEnroll');

        // Fungsi untuk mengupdate status tombol Daftar
        function updateEnrollButton() {
            const isAnyChecked = Array.from(checkItems).some(item => item.checked);
            btnEnroll.disabled = !isAnyChecked;
        }

        // Event listener untuk 'Check All'
        if (checkAll) {
            checkAll.addEventListener('change', function () {
                checkItems.forEach(item => {
                    item.checked = checkAll.checked;
                });
                updateEnrollButton();
            });
        }

        // Event listener untuk item individual
        checkItems.forEach(item => {
            item.addEventListener('change', function () {
                // Update status tombol
                updateEnrollButton();

                // Update status 'Check All'
                if (checkAll) {
                    const allChecked = Array.from(checkItems).every(i => i.checked);
                    checkAll.checked = allChecked;
                }
            });
        });
        
        // Panggil saat load pertama kali (jika ada input yang terisi dari old() value)
        updateEnrollButton();
    });
</script>

@endsection