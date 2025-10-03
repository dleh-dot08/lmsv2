@extends('layouts.users.template')

@section('content')

@php
    // Persiapan data untuk kemudahan akses (mencegah error jika relasi null)
    $profile = $user->profile;
    $employeeDetail = $user->employeeDetail;
    $mentorDetail = $user->mentorDetail;
    $participantDetail = $user->participantDetail;
    
    // Konstanta Role ID (Harus sama dengan yang di Controller)
    const ROLE_ID_EMPLOYEE = 3; 
    const ROLE_ID_PARTICIPANT = 4;
    const ROLE_ID_MENTOR = 5;
    const ROLE_ID_SCHOOL_PIC = 6;
    
    $isEmployee = $user->role_id === ROLE_ID_EMPLOYEE;
    $isParticipant = $user->role_id === ROLE_ID_PARTICIPANT;
    $isMentor = $user->role_id === ROLE_ID_MENTOR;
    $isSchoolPic = $user->role_id === ROLE_ID_SCHOOL_PIC;
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Manajemen Pengguna /</span> Detail Pengguna
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Detail Lengkap Pengguna: **{{ $user->name }}**</h5>
                
                <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        {{-- Foto Profil --}}
                        <img src="{{ 
                            ($profile && $profile->profile_photo_path) 
                                ? asset('storage/' . $profile->profile_photo_path) 
                                : asset('assets/img/avatars/default.png') 
                        }}" 
                        alt="Foto Profil" class="d-block rounded" height="100" width="100" id="uploadedAvatar" />
                        
                        <div class="button-wrapper">
                            <h4 class="mb-0">{{ $user->name }}</h4>
                            <p class="text-muted mb-0">Role: <span class="badge bg-label-primary">{{ $user->role->name ?? 'N/A' }}</span></p>
                            <p class="text-muted mb-0">Email: **{{ $user->email }}**</p>
                            <div class="mt-3">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary me-2">
                                    <i class="bx bx-edit-alt me-1"></i> Edit Pengguna
                                </a>
                                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="my-0" />
                
                <div class="card-body">
                    <div class="row">

                        {{-- KOLOM 1: DATA UMUM & KONTAK --}}
                        <div class="col-lg-6 mb-4">
                            <h5 class="mb-3 text-primary"><i class="bx bx-id-card me-1"></i> Data Biodata Umum</h5>
                            <dl class="row">
                                <dt class="col-sm-4">Nomor Telepon:</dt>
                                <dd class="col-sm-8 text-muted">{{ $profile->phone_number ?? '-' }}</dd>

                                <dt class="col-sm-4">Tempat/Tgl Lahir:</dt>
                                <dd class="col-sm-8 text-muted">
                                    {{ $profile->birth_place ?? '-' }}, 
                                    {{ $profile->birth_date ? \Carbon\Carbon::parse($profile->birth_date)->isoFormat('D MMMM YYYY') : '-' }}
                                </dd>

                                <dt class="col-sm-4">Alamat:</dt>
                                <dd class="col-sm-8 text-muted">{{ $profile->address ?? '-' }}</dd>
                            </dl>
                        </div>

                        {{-- KOLOM 2: DETAIL SPESIFIK BERDASARKAN ROLE --}}
                        <div class="col-lg-6 mb-4">
                            <h5 class="mb-3 text-primary"><i class="bx bx-detail me-1"></i> Detail Spesifik Role</h5>
                            
                            {{-- DETAIL KARYAWAN (ROLE ID: 3) --}}
                            @if ($isEmployee && $employeeDetail)
                                <div class="alert alert-info p-3">
                                    <h6 class="alert-heading fw-bold mb-3"><i class="bx bx-briefcase me-1"></i> Detail Karyawan</h6>
                                    <dl class="row m-0">
                                        <dt class="col-sm-4">ID Karyawan:</dt>
                                        <dd class="col-sm-8 mb-1">{{ $employeeDetail->employee_id ?? '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Divisi:</dt>
                                        <dd class="col-sm-8 mb-1">{{ $employeeDetail->division ?? '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Tgl. Masuk:</dt>
                                        <dd class="col-sm-8 mb-1">{{ $employeeDetail->hire_date ? \Carbon\Carbon::parse($employeeDetail->hire_date)->isoFormat('D MMMM YYYY') : '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Kontak Darurat:</dt>
                                        <dd class="col-sm-8 mb-0">{{ $employeeDetail->emergency_contact ?? '-' }}</dd>
                                    </dl>
                                </div>
                            
                            {{-- DETAIL PESERTA (ROLE ID: 4) --}}
                            @elseif ($isParticipant && $participantDetail)
                                <div class="alert alert-success p-3">
                                    <h6 class="alert-heading fw-bold mb-3"><i class="bx bx-run me-1"></i> Detail Peserta</h6>
                                    <dl class="row m-0">
                                        <dt class="col-sm-4">Kategori:</dt>
                                        <dd class="col-sm-8 mb-1"><span class="badge bg-success">{{ ucfirst($participantDetail->category) }}</span></dd>
                                        
                                        @if ($participantDetail->category == 'siswa' && $participantDetail->nisn)
                                            <dt class="col-sm-4">NISN:</dt>
                                            <dd class="col-sm-8 mb-1">{{ $participantDetail->nisn }}</dd>
                                        @endif
                                        
                                        @if (in_array($participantDetail->category, ['siswa', 'mahasiswa']))
                                            <dt class="col-sm-4">Institusi:</dt>
                                            <dd class="col-sm-8 mb-1">{{ $participantDetail->institution_name ?? '-' }}</dd>
                                        @endif

                                        @if ($participantDetail->category == 'mahasiswa' && $participantDetail->major)
                                            <dt class="col-sm-4">Jurusan:</dt>
                                            <dd class="col-sm-8 mb-0">{{ $participantDetail->major }}</dd>
                                        @endif
                                    </dl>
                                </div>
                            
                            {{-- DETAIL MENTOR (ROLE ID: 5) --}}
                            @elseif ($isMentor && $mentorDetail)
                                <div class="alert alert-warning p-3">
                                    <h6 class="alert-heading fw-bold mb-3"><i class="bx bx-book-content me-1"></i> Detail Mentor</h6>
                                    
                                    <p class="mb-1"><span class="fw-bold">No. KTP:</span> {{ $mentorDetail->ktp_number ?? '-' }}</p>
                                    <p class="mb-1"><span class="fw-bold">No. NPWP:</span> {{ $mentorDetail->npwp_number ?? '-' }}</p>
                                    
                                    <h6 class="mt-3 mb-2 fw-bold text-dark">Data Bank</h6>
                                    <dl class="row m-0">
                                        <dt class="col-sm-4">Nama Bank:</dt>
                                        <dd class="col-sm-8 mb-1">{{ $mentorDetail->bank_name ?? '-' }}</dd>
                                        <dt class="col-sm-4">No. Rekening:</dt>
                                        <dd class="col-sm-8 mb-1">{{ $mentorDetail->account_number ?? '-' }}</dd>
                                        <dt class="col-sm-4">Nama Pemilik:</dt>
                                        <dd class="col-sm-8 mb-0">{{ $mentorDetail->account_holder ?? '-' }}</dd>
                                    </dl>
                                    
                                    <h6 class="mt-3 mb-2 fw-bold text-dark">Dokumen</h6>
                                    @if($mentorDetail->ktp_file_path)
                                        <p class="mb-1">File KTP: <a href="{{ asset('storage/' . $mentorDetail->ktp_file_path) }}" target="_blank" class="text-primary">Lihat File</a></p>
                                    @else
                                        <p class="mb-1 text-muted">File KTP: Belum Diunggah</p>
                                    @endif
                                    @if($mentorDetail->npwp_file_path)
                                        <p class="mb-0">File NPWP: <a href="{{ asset('storage/' . $mentorDetail->npwp_file_path) }}" target="_blank" class="text-primary">Lihat File</a></p>
                                    @else
                                         <p class="mb-0 text-muted">File NPWP: Belum Diunggah</p>
                                    @endif
                                </div>

                            {{-- DETAIL PIC SEKOLAH (ROLE ID: 6) --}}
                            @elseif ($isSchoolPic && $user->schools->isNotEmpty())
                                <div class="alert alert-danger p-3">
                                    <h6 class="alert-heading fw-bold mb-3"><i class="bx bx-building-house me-1"></i> Penugasan PIC Sekolah</h6>
                                    @foreach ($user->schools as $school)
                                        <p class="mb-1"><span class="fw-bold">Sekolah:</span> {{ $school->school_name ?? '-' }} (NPSN: {{ $school->npsn ?? '-' }})</p>
                                        <p class="mb-0"><span class="fw-bold">Jabatan:</span> {{ $school->pivot->position ?? '-' }}</p>
                                        @if (!$loop->last) <hr class="my-2"> @endif
                                    @endforeach
                                </div>

                            @else
                                <div class="alert alert-secondary">
                                    <i class="bx bx-info-circle me-1"></i> Tidak ada detail spesifik yang dicatat untuk peran ini, atau detail belum dilengkapi.
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection