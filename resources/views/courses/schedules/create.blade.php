@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Kelas / {{ $course->nama_kelas }} / Jadwal /</span> Tambah Pertemuan
    </h4>

    <div class="alert alert-info" role="alert">
        <strong>Periode Kelas:</strong> {{ $course->waktu_mulai->format('d M Y') }} sampai {{ $course->waktu_akhir->format('d M Y') }}
    </div>

    <div class="card">
        <h5 class="card-header">Formulir Tambah Pertemuan Baru</h5>
        <div class="card-body">
            
            {{-- Form untuk menyimpan data jadwal. Arahkan ke route courses.schedules.store --}}
            <form method="POST" action="{{ route('courses.schedules.store', $course->id) }}">
                @csrf
                
                <h6 class="mb-3 text-primary"><i class="bx bx-calendar-plus me-1"></i> Detail Sesi</h6>
                <hr class="mt-0">

                {{-- Pertemuan Ke (Otomatis/Manual) --}}
                <div class="mb-3">
                    <label for="pertemuan_ke" class="form-label">Pertemuan Ke- <span class="text-danger">*</span></label>
                    <input 
                        type="number" 
                        class="form-control @error('pertemuan_ke') is-invalid @enderror" 
                        id="pertemuan_ke" 
                        name="pertemuan_ke" 
                        {{-- Menggunakan nilai $nextPertemuan yang dihitung di Controller --}}
                        value="{{ old('pertemuan_ke', $nextPertemuan ?? '') }}" 
                        min="1" 
                        max="20"
                        placeholder="Contoh: 1, 2, 3..."
                        required
                    >
                    <div class="form-text">
                        Total maksimal pertemuan adalah 20. Pertemuan berikutnya adalah **{{ $nextPertemuan ?? '?' }}**.
                    </div>
                    @error('pertemuan_ke')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    {{-- Tanggal Pertemuan --}}
                    <div class="col-md-4 mb-3">
                        <label for="tanggal_pertemuan" class="form-label">Tanggal Sesi <span class="text-danger">*</span></label>
                        <input 
                            type="date" 
                            class="form-control @error('tanggal_pertemuan') is-invalid @enderror" 
                            id="tanggal_pertemuan" 
                            name="tanggal_pertemuan" 
                            value="{{ old('tanggal_pertemuan') }}" 
                            required
                        >
                        @error('tanggal_pertemuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Waktu Mulai --}}
                    <div class="col-md-4 mb-3">
                        <label for="waktu_mulai_sesi" class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                        <input 
                            type="time" 
                            class="form-control @error('waktu_mulai_sesi') is-invalid @enderror" 
                            id="waktu_mulai_sesi" 
                            name="waktu_mulai_sesi" 
                            value="{{ old('waktu_mulai_sesi') }}" 
                            required
                        >
                        @error('waktu_mulai_sesi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Waktu Akhir --}}
                    <div class="col-md-4 mb-3">
                        <label for="waktu_akhir_sesi" class="form-label">Waktu Akhir <span class="text-danger">*</span></label>
                        <input 
                            type="time" 
                            class="form-control @error('waktu_akhir_sesi') is-invalid @enderror" 
                            id="waktu_akhir_sesi" 
                            name="waktu_akhir_sesi" 
                            value="{{ old('waktu_akhir_sesi') }}" 
                            required
                        >
                        @error('waktu_akhir_sesi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Topik Materi --}}
                <div class="mb-3">
                    <label for="topik_materi" class="form-label">Topik Materi</label>
                    <input 
                        type="text" 
                        class="form-control @error('topik_materi') is-invalid @enderror" 
                        id="topik_materi" 
                        name="topik_materi" 
                        value="{{ old('topik_materi') }}" 
                        placeholder="Contoh: Pengenalan dasar teknik passing dan shooting"
                    >
                    @error('topik_materi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                {{-- Ruangan/Lokasi --}}
                <div class="mb-3">
                    <label for="ruangan" class="form-label">Ruangan / Lokasi</label>
                    <input 
                        type="text" 
                        class="form-control @error('ruangan') is-invalid @enderror" 
                        id="ruangan" 
                        name="ruangan" 
                        value="{{ old('ruangan') }}" 
                        placeholder="Contoh: Gedung A, Ruang 101, atau Link Zoom"
                    >
                    @error('ruangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <hr class="mt-4">

                {{-- Tombol Aksi --}}
                <a href="{{ route('courses.schedules.index', $course->id) }}" class="btn btn-secondary me-2">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    Simpan Pertemuan
                </button>
            </form>
        </div>
    </div>
</div>

@endsection