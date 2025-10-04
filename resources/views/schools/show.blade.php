@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Master Data / Data Sekolah /</span> Detail
    </h4>

    <div class="row">
        {{-- Bagian Kiri: Detail Sekolah --}}
        <div class="col-lg-8 col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Detail Sekolah: {{ $school->school_name }}</h5>
                <div class="card-body">
                    
                    {{-- Data Utama --}}
                    <h6 class="mb-3 text-primary"><i class="bx bx-info-circle me-1"></i> Informasi Dasar</h6>
                    <dl class="row">
                        <dt class="col-sm-4">Nama Sekolah:</dt>
                        <dd class="col-sm-8"><strong>{{ $school->school_name }}</strong></dd>

                        <dt class="col-sm-4">NPSN:</dt>
                        <dd class="col-sm-8">{{ $school->npsn }}</dd>

                        <dt class="col-sm-4">Jenjang Pendidikan:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-label-info">{{ $school->level->name ?? 'N/A' }}</span>
                        </dd>
                        
                        <dt class="col-sm-4">Kepala Sekolah:</dt>
                        <dd class="col-sm-8">{{ $school->headmaster_name }}</dd>
                    </dl>

                    <hr class="my-4">

                    {{-- Data Alamat --}}
                    <h6 class="mb-3 text-primary"><i class="bx bx-map-pin me-1"></i> Lokasi</h6>
                    <dl class="row">
                        <dt class="col-sm-4">Kota/Kabupaten:</dt>
                        <dd class="col-sm-8">{{ $school->city ?? '-' }}</dd>

                        <dt class="col-sm-4">Alamat Lengkap:</dt>
                        <dd class="col-sm-8">{{ $school->full_address ?? '-' }}</dd>
                    </dl>

                    <div class="mt-4">
                        <a href="{{ route('schools.edit', $school->id) }}" class="btn btn-warning me-2"><i class="bx bx-edit-alt me-1"></i> Edit Data</a>
                        <a href="{{ route('schools.index') }}" class="btn btn-outline-secondary">Kembali ke Daftar</a>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Bagian Kanan: PIC Sekolah --}}
        <div class="col-lg-4 col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">PIC (Person in Charge) Sekolah</h5>
                <div class="card-body">
                    @php 
                        // Ambil PIC pertama (asumsi hanya ada satu PIC per sekolah)
                        $pic = $school->pics->first(); 
                    @endphp

                    @if ($pic)
                        <div class="text-center mb-3">
                            {{-- Anda bisa menampilkan foto profil PIC di sini jika ada --}}
                            <div class="avatar avatar-lg me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">{{ strtoupper(substr($pic->name, 0, 2)) }}</span>
                            </div>
                        </div>

                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="bx bx-user me-2"></i> 
                                <strong>Nama:</strong> {{ $pic->name }}
                            </li>
                            <li class="list-group-item">
                                <i class="bx bx-briefcase me-2"></i> 
                                <strong>Jabatan:</strong> <span class="text-info">{{ $pic->pivot->position }}</span>
                            </li>
                            <li class="list-group-item">
                                <i class="bx bx-envelope me-2"></i> 
                                <strong>Email:</strong> {{ $pic->email }}
                            </li>
                            <li class="list-group-item">
                                <i class="bx bx-phone me-2"></i> 
                                <strong>Telepon:</strong> {{ $pic->profile->phone_number ?? '-' }}
                            </li>
                        </ul>
                    @else
                        <div class="alert alert-danger">
                            <i class="bx bx-x-circle me-1"></i> **Belum ada PIC** yang ditugaskan untuk sekolah ini.
                            <a href="{{ route('schools.edit', $school->id) }}" class="alert-link d-block mt-2">Segera Tugaskan PIC</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection