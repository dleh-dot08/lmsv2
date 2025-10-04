@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Master Data / Data Sekolah /</span> Detail & Peserta
    </h4>
    
    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
                        <a href="{{ route('schools.index') }}" class="btn btn-outline-secondary">Kembali ke Daftar Sekolah</a>
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
                        $pic = $school->pics->first(); 
                    @endphp

                    @if ($pic)
                        <div class="text-center mb-3">
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

    {{-- BARU: BAGIAN DAFTAR PESERTA DIDIK --}}
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Peserta Didik (Total: {{ $students->total() }})</h5>
                    {{-- Tombol Tambah Peserta --}}
                    <a href="{{ route('students.create', ['school_id' => $school->id]) }}" class="btn btn-sm btn-success">
                        <i class="bx bx-plus me-1"></i> Tambah Peserta
                    </a>

                    {{-- Tombol 2: Menugaskan Peserta Didik yang Sudah Ada (Assign) --}}
                    <a href="{{ route('students.assign.form') }}" class="btn btn-sm btn-info">
                        <i class="bx bx-link me-1"></i> Tugaskan Peserta Existing
                    </a>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No.</th>
                                <th>Nama Peserta (ID)</th>
                                <th>Jenjang / Kelas</th>
                                <th>Email</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($students as $index => $student)
                                <tr>
                                    <td>{{ $students->firstItem() + $index }}</td>
                                    <td>
                                        <strong>{{ $student->name }}</strong> <br>
                                        <small class="text-muted">ID: {{ $student->participantDetail->nisn ?? '-' }}</small>
                                    </td>
                                    <td>
                                        {{ $student->participantDetail->level->name ?? 'N/A' }} / 
                                        <strong>{{ $student->participantDetail->grade->name ?? '-' }}</strong>
                                    </td>
                                    <td>{{ $student->email }}</td>
                                    <td>
                                        <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-info" title="Detail Peserta"><i class="bx bx-detail"></i></a>
                                        <a href="{{ route('students.edit', $student->id) }}" class="btn btn-sm btn-warning" title="Edit Peserta"><i class="bx bx-edit-alt"></i></a>
                                        {{-- Anda bisa tambahkan form delete di sini --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada peserta didik terdaftar di sekolah ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer clearfix">
                    {{ $students->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection