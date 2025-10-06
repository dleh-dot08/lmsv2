@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Semester /</span> Tambah Semester Baru
    </h4>

    <div class="card">
        <h5 class="card-header">Formulir Tambah Semester</h5>
        <div class="card-body">
            
            {{-- Form untuk menyimpan data baru. Arahkan ke route semesters.store --}}
            <form method="POST" action="{{ route('semesters.store') }}">
                @csrf
                
                {{-- Nama Semester --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Semester <span class="text-danger">*</span></label>
                    <input 
                        type="text" 
                        class="form-control @error('name') is-invalid @enderror" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}" 
                        placeholder="Contoh: Ganjil 2025/2026"
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tahun Ajaran --}}
                <div class="mb-3">
                    <label for="academic_year" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                    <input 
                        type="text" 
                        class="form-control @error('academic_year') is-invalid @enderror" 
                        id="academic_year" 
                        name="academic_year" 
                        value="{{ old('academic_year') }}" 
                        placeholder="Contoh: 2025/2026"
                        required
                    >
                    @error('academic_year')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    {{-- Tanggal Mulai --}}
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input 
                            type="date" 
                            class="form-control @error('start_date') is-invalid @enderror" 
                            id="start_date" 
                            name="start_date" 
                            value="{{ old('start_date') }}" 
                            required
                        >
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tanggal Berakhir --}}
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">Tanggal Berakhir <span class="text-danger">*</span></label>
                        <input 
                            type="date" 
                            class="form-control @error('end_date') is-invalid @enderror" 
                            id="end_date" 
                            name="end_date" 
                            value="{{ old('end_date') }}" 
                            required
                        >
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Status Aktif --}}
                <div class="mb-4 form-check">
                    <input 
                        type="checkbox" 
                        class="form-check-input" 
                        id="is_active" 
                        name="is_active" 
                        value="1" 
                        {{ old('is_active') ? 'checked' : '' }} 
                    >
                    <label class="form-check-label" for="is_active">
                        Jadikan Semester Aktif saat ini 
                        <span class="text-muted small ms-2">(Jika dicentang, semester lain yang aktif akan dinonaktifkan secara otomatis.)</span>
                    </label>
                    @error('is_active')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                
                <hr>

                {{-- Tombol Aksi --}}
                <a href="{{ route('semesters.index') }}" class="btn btn-secondary me-2">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    Simpan Semester
                </button>
            </form>
        </div>
    </div>
</div>

@endsection