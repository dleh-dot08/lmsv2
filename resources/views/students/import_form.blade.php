@extends('layouts.users.template') 

@section('content')
<div class="container py-5">
    <h1 class="mb-4 text-dark fw-bold">Bulk Data Import Peserta Didik</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- CARD: UPLOAD FILE --}}
    <div class="card shadow-lg border-0 mb-5">
        {{-- Mengubah warna header agar lebih lembut --}}
        <div class="card-header bg-light border-bottom">
            <h5 class="mb-0 text-primary fw-bold">Langkah 1: Upload File</h5>
        </div>
        <div class="card-body p-4">
            
            <div class="mb-4 pb-3 border-bottom">
                <h6 class="text-secondary fw-semibold">Download Format Template:</h6>
                
                {{-- Menggunakan tombol yang lebih jelas (outline) --}}
                <a href="{{ route('students.download_template', ['type' => 'create_assign']) }}" class="btn btn-outline-success me-2">
                    <i class="fas fa-user-plus"></i> Template Create/Assign
                </a>
                
                <a href="{{ route('students.download_template', ['type' => 'grade_update']) }}" class="btn btn-outline-info">
                    <i class="fas fa-graduation-cap"></i> Template Kenaikan Kelas
                </a>
                <p class="mt-2 text-muted small">Pastikan header kolom sesuai dengan template yang diunduh.</p>
            </div>

            <form action="{{ route('students.upload_file') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group mb-3">
                    <label for="import_type" class="form-label fw-semibold">Pilih Tipe Impor:</label>
                    <select name="import_type" id="import_type" class="form-control" required>
                        <option value="" disabled selected>-- Pilih Tipe --</option>
                        <option value="create_assign">1. Bulk Create Peserta & Initial Assignment</option>
                        <option value="grade_update">2. Bulk Update Kelas (Kenaikan Kelas)</option>
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label for="file" class="form-label fw-semibold">Pilih File CSV/Excel yang sudah diisi:</label>
                    <input type="file" name="file" id="file" class="form-control" required>
                    <small class="form-text text-muted">Maksimal ukuran file: 2MB.</small>
                </div>

                {{-- Mengubah tombol utama agar lebih menonjol --}}
                <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">
                    <i class="fas fa-cloud-upload-alt"></i> Upload File & Lakukan Validasi ke Staging
                </button>
            </form>
        </div>
    </div>

    {{-- CARD: RIWAYAT IMPORT --}}
    <div class="card shadow-lg border-0">
        {{-- Mengubah warna header agar netral --}}
        <div class="card-header bg-light border-bottom">
            <h5 class="mb-0 text-secondary fw-bold">Langkah 2: Riwayat dan Review Import</h5>
        </div>
        <div class="card-body p-4">
            @if($recentImports->isEmpty())
                <div class="alert alert-light text-center m-0">
                    <i class="fas fa-info-circle me-2"></i> Belum ada riwayat import yang tersimpan.
                </div>
            @else
                <ul class="list-group list-group-flush">
                    @foreach($recentImports as $import)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <strong class="text-primary">{{ $import->import_type == 'create_assign' ? 'CREATE / ASSIGN' : 'KENAIKAN KELAS' }}</strong>
                                <small class="d-block text-muted">Diunggah: {{ $import->created_at->format('d M Y H:i') }}</small>
                            </div>
                            <a href="{{ route('students.review', $import->batch_token) }}" class="btn btn-sm btn-warning shadow-sm">
                                <i class="fas fa-search me-1"></i> Review & Commit
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
