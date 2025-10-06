@extends('layouts.users.template')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Edit Data Peserta Didik: {{ $student->name }}</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            
            {{-- Form akan mengarahkan ke route update --}}
            <form action="{{ route('students.update', $student->id) }}" method="POST">
                @csrf
                @method('PUT') {{-- Gunakan method spoofing PUT untuk update --}}

                {{-- INFORMASI AKUN --}}
                <fieldset class="mb-5 p-3 border rounded">
                    <legend class="float-none w-auto px-1 fs-5 text-primary">Informasi Akun (User)</legend>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $student->name) }}" required>
                            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $student->email) }}" required>
                            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nisn" class="form-label">NISN</label>
                            <input type="text" name="nisn" id="nisn" class="form-control" value="{{ old('nisn', $student->nisn) }}" required>
                            @error('nisn')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Ubah Password (Kosongkan jika tidak diubah)</label>
                            <input type="password" name="password" id="password" class="form-control">
                            @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </fieldset>

                {{-- INFORMASI DETAIL PESERTA --}}
                <fieldset class="mb-5 p-3 border rounded">
                    <legend class="float-none w-auto px-1 fs-5 text-success">Detail Peserta (Participant Details)</legend>
                    
                    @php
                        // Ambil detail peserta atau buat objek kosong jika belum ada
                        $detail = $student->participantDetail ?? new \stdClass();
                    @endphp

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="school_id" class="form-label">Sekolah</label>
                            <select name="school_id" id="school_id" class="form-control" required>
                                <option value="" disabled>-- Pilih Sekolah --</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school->id }}" 
                                        {{ old('school_id', $detail->school_id ?? null) == $school->id ? 'selected' : '' }}>
                                        {{ $school->name }} (NPSN: {{ $school->npsn }})
                                    </option>
                                @endforeach
                            </select>
                            @error('school_id')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="grade_id" class="form-label">Kelas/Tingkatan</label>
                            <select name="grade_id" id="grade_id" class="form-control" required>
                                <option value="" disabled>-- Pilih Kelas --</option>
                                @foreach($grades as $grade)
                                    <option value="{{ $grade->id }}" 
                                        {{ old('grade_id', $detail->grade_id ?? null) == $grade->id ? 'selected' : '' }}>
                                        {{ $grade->name }} ({{ $grade->level->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('grade_id')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="major" class="form-label">Jurusan/Peminatan</label>
                            <input type="text" name="major" id="major" class="form-control" value="{{ old('major', $detail->major ?? '') }}">
                            @error('major')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Kategori</label>
                            <select name="category" id="category" class="form-control" required>
                                @php $currentCategory = old('category', $detail->category ?? 'siswa'); @endphp
                                <option value="siswa" {{ $currentCategory == 'siswa' ? 'selected' : '' }}>Siswa</option>
                                <option value="mahasiswa" {{ $currentCategory == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                <option value="umum" {{ $currentCategory == 'umum' ? 'selected' : '' }}>Umum</option>
                            </select>
                            @error('category')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </fieldset>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
