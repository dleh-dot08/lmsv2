@extends('layouts.users.template') {{-- Ganti dengan layout utama Anda jika berbeda --}}

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Peserta Didik /</span> Tambah Peserta Baru
    </h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <h6>Data Gagal Disimpan! Periksa Peringatan di Bawah.</h6>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <h5 class="card-header">Formulir Pendaftaran Peserta Didik</h5>
        <div class="card-body">
            <form method="POST" action="{{ route('students.store') }}">
                @csrf
                
                <h6 class="mb-4 text-primary"><i class="bx bx-user me-1"></i> Data Akun & Pribadi</h6>
                <div class="row g-3">
                    {{-- Nama Lengkap --}}
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    {{-- Email --}}
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email (Username)</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    {{-- Password --}}
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>

                    {{-- NISN / ID Peserta --}}
                    <div class="col-md-6">
                        <label for="nisn" class="form-label">NISN / ID Peserta</label>
                        <input type="text" class="form-control @error('nisn') is-invalid @enderror" id="nisn" name="nisn" value="{{ old('nisn') }}" placeholder="Opsional, jika ada NISN/NIM">
                        @error('nisn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    {{-- Kategori Peserta --}}
                    <div class="col-md-6">
                        <label for="category" class="form-label">Kategori Peserta</label>
                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                            <option value="">Pilih Kategori</option>
                            {{-- Sesuaikan opsi kategori Anda (siswa, mahasiswa, umum, dll.) --}}
                            <option value="siswa" {{ old('category') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                            <option value="mahasiswa" {{ old('category') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="umum" {{ old('category') == 'umum' ? 'selected' : '' }}>Umum</option>
                        </select>
                        @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="mb-4 text-primary"><i class="bx bx-building me-1"></i> Data Institusi</h6>
                <div class="row g-3">
                    {{-- Institusi (Sekolah) --}}
                    <div class="col-md-12">
                        <label for="school_id" class="form-label">Sekolah/Institusi Asal</label>
                        <select class="form-select @error('school_id') is-invalid @enderror" id="school_id" name="school_id" required>
                            <option value="">Pilih Sekolah</option>
                            @foreach ($schools as $school)
                                <option value="{{ $school->id }}" {{ old('school_id', request('school_id')) == $school->id ? 'selected' : '' }}>
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
                    <button type="submit" class="btn btn-primary me-2">Daftarkan Peserta</button>
                    <a href="{{ route('schools.show', request('school_id')) }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection