@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Kursus & Kelas /</span> Tambah Kursus Baru
    </h4>

    <div class="card">
        <h5 class="card-header">Formulir Pembuatan Kursus/Kelas</h5>
        <div class="card-body">
            
            {{-- Form untuk menyimpan data baru. Arahkan ke route courses.store --}}
            <form method="POST" action="{{ route('courses.store') }}">
                @csrf
                
                {{-- BAGIAN 1: DETAIL UTAMA KELAS --}}
                <h6 class="mb-3 text-primary"><i class="bx bx-book-content me-1"></i> Detail Umum</h6>
                <hr class="mt-0">

                {{-- Nama Kelas --}}
                <div class="mb-3">
                    <label for="nama_kelas" class="form-label">Nama Kursus/Kelas <span class="text-danger">*</span></label>
                    <input 
                        type="text" 
                        class="form-control @error('nama_kelas') is-invalid @enderror" 
                        id="nama_kelas" 
                        name="nama_kelas" 
                        value="{{ old('nama_kelas') }}" 
                        placeholder="Contoh: Basket Intensif Level A"
                        required
                    >
                    @error('nama_kelas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi Kelas</label>
                    <textarea 
                        class="form-control @error('deskripsi') is-invalid @enderror" 
                        id="deskripsi" 
                        name="deskripsi" 
                        rows="3" 
                        placeholder="Deskripsi singkat, tujuan, dan materi yang akan dipelajari."
                    >{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <br>
                {{-- BAGIAN 2: SPESIFIKASI DAN WAKTU --}}
                <h6 class="mb-3 text-primary"><i class="bx bx-tag me-1"></i> Spesifikasi & Periode Waktu</h6>
                <hr class="mt-0">

                <div class="row">
                    {{-- Category ID --}}
                    <div class="col-md-4 mb-3">
                        <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->type ?? 'Umum' }})
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Semester ID --}}
                    <div class="col-md-4 mb-3">
                        <label for="semester_id" class="form-label">Semester <span class="text-danger">*</span></label>
                        <select class="form-select @error('semester_id') is-invalid @enderror" id="semester_id" name="semester_id" required>
                            <option value="">-- Pilih Semester --</option>
                            @foreach ($semesters as $semester)
                                <option value="{{ $semester->id }}" {{ old('semester_id') == $semester->id ? 'selected' : '' }}>
                                    {{ $semester->name }} {{ $semester->is_active ? '(AKTIF)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('semester_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Level Kesulitan (ENUM) --}}
                    <div class="col-md-4 mb-3">
                        <label for="level" class="form-label">Level Kesulitan <span class="text-danger">*</span></label>
                        <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                            <option value="Beginner" {{ old('level') == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="Intermediate" {{ old('level') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="Advanced" {{ old('level') == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                        </select>
                        @error('level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Level ID (Jenjang) --}}
                    <div class="col-md-4 mb-3">
                        <label for="level_id" class="form-label">Jenjang <span class="text-danger">*</span></label>
                        <select class="form-select @error('level_id') is-invalid @enderror" id="level_id" name="level_id" required>
                            <option value="">-- Pilih Jenjang --</option>
                            @foreach ($levels as $level)
                                <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>
                                    {{ $level->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('level_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Grade ID (Kelas Fisik) --}}
                    <div class="col-md-4 mb-3">
                        <label for="grade_id" class="form-label">Kelas Fisik <span class="text-danger">*</span></label>
                        <select class="form-select @error('grade_id') is-invalid @enderror" id="grade_id" name="grade_id" required>
                            <option value="">-- Pilih Kelas Fisik --</option>
                            @foreach ($grades as $grade)
                                <option value="{{ $grade->id }}" {{ old('grade_id') == $grade->id ? 'selected' : '' }}>
                                    {{ $grade->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('grade_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Status Kelas</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="Aktif" {{ old('status', 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Nonaktif" {{ old('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            <option value="Penuh" {{ old('status') == 'Penuh' ? 'selected' : '' }}>Penuh</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Waktu Mulai Keseluruhan --}}
                    <div class="col-md-6 mb-3">
                        <label for="waktu_mulai" class="form-label">Tanggal Mulai Kelas <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('waktu_mulai') is-invalid @enderror" id="waktu_mulai" name="waktu_mulai" value="{{ old('waktu_mulai') }}" required>
                        @error('waktu_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Waktu Akhir Keseluruhan --}}
                    <div class="col-md-6 mb-3">
                        <label for="waktu_akhir" class="form-label">Tanggal Akhir Kelas <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('waktu_akhir') is-invalid @enderror" id="waktu_akhir" name="waktu_akhir" value="{{ old('waktu_akhir') }}" required>
                        @error('waktu_akhir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <br>
                {{-- BAGIAN 3: PENUGASAN MENTOR --}}
                <h6 class="mb-3 text-primary"><i class="bx bx-group me-1"></i> Penugasan Mentor</h6>
                <hr class="mt-0">
                <p class="text-muted small">Semua mentor harus unik. Hanya boleh ada satu Mentor Utama.</p>

                <div class="row">
                    {{-- Mentor Utama --}}
                    <div class="col-md-4 mb-3">
                        <label for="mentor_utama" class="form-label">Mentor Utama <span class="text-danger">*</span></label>
                        <select class="form-select @error('mentor_utama') is-invalid @enderror" id="mentor_utama" name="mentor_utama" required>
                            <option value="">-- Pilih Mentor Utama --</option>
                            @foreach ($mentors as $mentor)
                                <option value="{{ $mentor->id }}" {{ old('mentor_utama') == $mentor->id ? 'selected' : '' }}>
                                    {{ $mentor->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('mentor_utama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Mentor Pengganti --}}
                    <div class="col-md-4 mb-3">
                        <label for="mentor_pengganti" class="form-label">Mentor Pengganti</label>
                        <select class="form-select @error('mentor_pengganti') is-invalid @enderror" id="mentor_pengganti" name="mentor_pengganti">
                            <option value="">-- Pilih Mentor Pengganti --</option>
                            @foreach ($mentors as $mentor)
                                <option value="{{ $mentor->id }}" {{ old('mentor_pengganti') == $mentor->id ? 'selected' : '' }}>
                                    {{ $mentor->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('mentor_pengganti')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Mentor Cadangan --}}
                    <div class="col-md-4 mb-3">
                        <label for="mentor_cadangan" class="form-label">Mentor Cadangan</label>
                        <select class="form-select @error('mentor_cadangan') is-invalid @enderror" id="mentor_cadangan" name="mentor_cadangan">
                            <option value="">-- Pilih Mentor Cadangan --</option>
                            @foreach ($mentors as $mentor)
                                <option value="{{ $mentor->id }}" {{ old('mentor_cadangan') == $mentor->id ? 'selected' : '' }}>
                                    {{ $mentor->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('mentor_cadangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <hr class="mt-4">

                {{-- Tombol Aksi --}}
                <a href="{{ route('courses.index') }}" class="btn btn-secondary me-2">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    Simpan Kursus Baru
                </button>
            </form>
        </div>
    </div>
</div>

@endsection