@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Kelas / {{ $course->nama_kelas }} / Jadwal /</span> Edit Pertemuan Ke-{{ $schedule->pertemuan_ke }}
    </h4>

    <div class="alert alert-info" role="alert">
        <strong>Periode Kelas:</strong> {{ $course->waktu_mulai->format('d M Y') }} sampai {{ $course->waktu_akhir->format('d M Y') }}
    </div>

    <div class="card">
        <h5 class="card-header">Formulir Edit Pertemuan</h5>
        <div class="card-body">
            
            {{-- Form untuk update data jadwal. Arahkan ke route courses.schedules.update --}}
            <form method="POST" action="{{ route('courses.schedules.update', [$course->id, $schedule->id]) }}">
                @csrf
                @method('PUT')
                
                <h6 class="mb-3 text-primary"><i class="bx bx-calendar-edit me-1"></i> Detail Sesi</h6>
                <hr class="mt-0">

                {{-- Pertemuan Ke (Biasanya tidak diubah, tapi tetap ditampilkan) --}}
                <div class="mb-3">
                    <label for="pertemuan_ke" class="form-label">Pertemuan Ke- <span class="text-danger">*</span></label>
                    <input 
                        type="number" 
                        class="form-control @error('pertemuan_ke') is-invalid @enderror" 
                        id="pertemuan_ke" 
                        name="pertemuan_ke" 
                        value="{{ old('pertemuan_ke', $schedule->pertemuan_ke) }}" 
                        min="1" 
                        max="20"
                        placeholder="Contoh: 1, 2, 3..."
                        required
                        readonly {{-- Agar nomor pertemuan tidak mudah diubah --}}
                    >
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
                            {{-- Mengisi nilai dari data yang sudah ada --}}
                            value="{{ old('tanggal_pertemuan', $schedule->tanggal_pertemuan->format('Y-m-d')) }}" 
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
                            {{-- Waktu harus dalam format string H:i:s, gunakan substr untuk H:i --}}
                            value="{{ old('waktu_mulai_sesi', substr($schedule->waktu_mulai_sesi, 0, 5)) }}" 
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
                            value="{{ old('waktu_akhir_sesi', substr($schedule->waktu_akhir_sesi, 0, 5)) }}" 
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
                        value="{{ old('topik_materi', $schedule->topik_materi) }}" 
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
                        value="{{ old('ruangan', $schedule->ruangan) }}" 
                        placeholder="Contoh: Gedung A, Ruang 101, atau Link Zoom"
                    >
                    @error('ruangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tambahkan di bawah field Ruangan/Lokasi --}}
                <h6 class="mb-3 text-danger mt-4"><i class="bx bx-history me-1"></i> Riwayat & Alasan Perubahan</h6>
                <hr class="mt-0">

                <div class="mb-3">
                    <label for="alasan_perubahan" class="form-label">Alasan Perubahan Jadwal/Materi <span class="text-danger">*</span></label>
                    <textarea 
                        class="form-control @error('alasan_perubahan') is-invalid @enderror" 
                        id="alasan_perubahan" 
                        name="alasan_perubahan" 
                        rows="3" 
                        placeholder="Contoh: Tanggal dimajukan karena libur nasional. Topik materi diubah sesuai permintaan mentor."
                        required
                    >{{ old('alasan_perubahan') }}</textarea>
                    @error('alasan_perubahan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Alasan wajib diisi untuk mencatat riwayat perubahan jadwal.
                    </div>
                </div>
                {{-- End of Alasan Perubahan --}}
                
                <hr class="mt-4">

                {{-- Tombol Aksi --}}
                <a href="{{ route('courses.schedules.index', $course->id) }}" class="btn btn-secondary me-2">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    Update Pertemuan
                </button>
            </form>
        </div>
    </div>
</div>

@endsection