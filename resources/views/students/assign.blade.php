@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    {{-- BARU: TEMPAT MENAMPILKAN SEMUA ERROR VALIDASI --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <h6 class="alert-heading d-flex align-items-center"><i class="bx bx-error me-2"></i> **Data Gagal Disimpan!**</h6>
            <p class="mb-0">Mohon periksa dan perbaiki kesalahan pada input berikut:</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    {{-- AKHIR BLOK ERROR --}}

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Peserta Didik /</span> Penugasan Sekolah
    </h4>

    <div class="card mb-4">
        <h5 class="card-header">Formulir Penugasan Sekolah ke Peserta Didik yang Sudah Ada</h5>
        <div class="card-body">
            <form method="POST" action="{{ route('students.assign.store') }}">
                @csrf
                
                <h6 class="mb-4 text-danger"><i class="bx bx-error-alt me-1"></i> Pilih Peserta Didik</h6>
                <div class="row g-3 mb-5">
                    {{-- Pilih User Peserta Didik --}}
                    <div class="col-md-12">
                        <label for="user_id" class="form-label">Peserta Didik (User role 4) yang Akan Ditugaskan</label>
                        <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                            <option value="">Pilih Peserta Didik (Total: {{ $unassignedStudents->count() }})</option>
                            @foreach ($unassignedStudents as $student)
                                <option value="{{ $student->id }}" {{ old('user_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }} ({{ $student->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <h6 class="mb-4 text-primary"><i class="bx bx-building me-1"></i> Data Institusi yang Ditugaskan</h6>
                <div class="row g-3">
                    
                    {{-- NISN / ID Peserta --}}
                    <div class="col-md-6">
                        <label for="nisn" class="form-label">NISN / ID Peserta (Harus Unik)</label>
                        <input type="text" class="form-control @error('nisn') is-invalid @enderror" id="nisn" name="nisn" value="{{ old('nisn') }}" placeholder="Wajib diisi jika user belum punya ID">
                        @error('nisn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Kategori Peserta --}}
                    <div class="col-md-6">
                        <label for="category" class="form-label">Kategori Peserta</label>
                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                            <option value="">Pilih Kategori</option>
                            <option value="siswa" {{ old('category') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                            <option value="mahasiswa" {{ old('category') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="umum" {{ old('category') == 'umum' ? 'selected' : '' }}>Umum</option>
                        </select>
                        @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    {{-- Nama Institusi (Jika tidak ada school_id) --}}
                    <div class="col-md-12">
                        <label for="institution_name" class="form-label">Nama Institusi</label>
                        <input type="text" class="form-control @error('institution_name') is-invalid @enderror" id="institution_name" name="institution_name" value="{{ old('institution_name') }}" placeholder="Nama Sekolah/Kampus">
                        @error('institution_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>


                    {{-- Institusi (Sekolah) --}}
                    <div class="col-md-12">
                        <label for="school_id" class="form-label">Pilih Sekolah yang Ditugaskan</label>
                        <select class="form-select @error('school_id') is-invalid @enderror" id="school_id" name="school_id" required>
                            <option value="">Pilih Sekolah</option>
                            @foreach ($schools as $school)
                                <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                                    {{ $school->school_name }} ({{ $school->level->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('school_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Jenjang --}}
                    <div class="col-md-4">
                        <label for="level_id" class="form-label">Jenjang Pendidikan</label>
                        <select class="form-select @error('level_id') is-invalid @enderror" id="level_id" name="level_id" required>
                            <option value="">Pilih Jenjang</option>
                            @foreach ($levels as $level)
                                <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>
                                    {{ $level->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('level_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    {{-- Kelas/Tingkatan --}}
                    <div class="col-md-4">
                        <label for="grade_id" class="form-label">Kelas/Tingkatan</label>
                        <select class="form-select @error('grade_id') is-invalid @enderror" id="grade_id" name="grade_id" required>
                            <option value="">Pilih Kelas</option>
                            @foreach ($grades as $grade)
                                <option value="{{ $grade->id }}" {{ old('grade_id') == $grade->id ? 'selected' : '' }}>
                                    {{ $grade->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('grade_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    {{-- Jurusan/Major --}}
                    <div class="col-md-4">
                        <label for="major" class="form-label">Jurusan/Bidang (Opsional)</label>
                        <input type="text" class="form-control @error('major') is-invalid @enderror" id="major" name="major" value="{{ old('major') }}">
                        @error('major') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>


                <div class="mt-4">
                    <button type="submit" class="btn btn-primary me-2">Lakukan Penugasan</button>
                    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection