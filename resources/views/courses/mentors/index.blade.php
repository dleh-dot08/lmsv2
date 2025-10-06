@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Kelas / {{ $course->nama_kelas }} /</span> Kelola Penugasan Mentor
    </h4>

    {{-- Pesan Notifikasi Sukses/Error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        {{-- Kolom Kiri: Mentor yang Ditugaskan --}}
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="m-0">Mentor Aktif Ditugaskan ({{ $mentors->count() }} Orang)</h5>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Nama Mentor</th>
                                <th>ID User</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($mentors as $mentor)
                                <tr>
                                    <td>
                                        <span class="badge bg-label-{{ $mentor->pivot->role == 'Utama' ? 'primary' : 'secondary' }}">
                                            {{ $mentor->pivot->role }}
                                        </span>
                                    </td>
                                    <td><strong>{{ $mentor->name }}</strong></td>
                                    <td>#{{ $mentor->id }}</td>
                                    <td>
                                        {{-- Hanya mentor Pengganti/Cadangan yang boleh dilepas --}}
                                        @if ($mentor->pivot->role != 'Utama')
                                            {{-- Form untuk Melepas Penugasan (Detaching) --}}
                                            <form action="{{ route('courses.mentors.destroy', [$course->id, $mentor->id]) }}" method="POST" onsubmit="return confirm('Yakin melepas penugasan mentor {{ $mentor->name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    Lepas
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-primary small">Role Utama</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-danger">Belum ada mentor yang ditugaskan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Form Penambahan/Perubahan Mentor --}}
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="m-0">Tambah/Ubah Penugasan</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Gunakan formulir ini untuk mengubah atau menugaskan peran baru (Utama, Pengganti, Cadangan).</p>

                    {{-- Form untuk menyinkronkan/mengubah tugas mentor --}}
                    <form method="POST" action="{{ route('courses.mentors.store', $course->id) }}">
                        @csrf
                        
                        {{-- Data Mentor yang saat ini bertugas untuk pre-select --}}
                        @php
                            $currentMentorRoles = $mentors->keyBy('pivot.role');
                            $mentorUtamaId = $currentMentorRoles['Utama']->id ?? null;
                            $mentorPenggantiId = $currentMentorRoles['Pengganti']->id ?? null;
                            $mentorCadanganId = $currentMentorRoles['Cadangan']->id ?? null;
                        @endphp

                        {{-- Mentor Utama (Wajib ada) --}}
                        <div class="mb-3">
                            <label for="mentor_utama" class="form-label">Mentor Utama <span class="text-danger">*</span></label>
                            <select class="form-select @error('mentor_utama') is-invalid @enderror" id="mentor_utama" name="mentor_utama" required>
                                <option value="">-- Pilih Mentor Utama --</option>
                                @foreach ($availableMentors as $mentor)
                                    <option 
                                        value="{{ $mentor->id }}" 
                                        {{ old('mentor_utama', $mentorUtamaId) == $mentor->id ? 'selected' : '' }}
                                    >
                                        {{ $mentor->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mentor_utama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Mentor Pengganti --}}
                        <div class="mb-3">
                            <label for="mentor_pengganti" class="form-label">Mentor Pengganti</label>
                            <select class="form-select @error('mentor_pengganti') is-invalid @enderror" id="mentor_pengganti" name="mentor_pengganti">
                                <option value="">-- Pilih Mentor Pengganti --</option>
                                @foreach ($availableMentors as $mentor)
                                    <option 
                                        value="{{ $mentor->id }}" 
                                        {{ old('mentor_pengganti', $mentorPenggantiId) == $mentor->id ? 'selected' : '' }}
                                    >
                                        {{ $mentor->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mentor_pengganti')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Mentor Cadangan --}}
                        <div class="mb-3">
                            <label for="mentor_cadangan" class="form-label">Mentor Cadangan</label>
                            <select class="form-select @error('mentor_cadangan') is-invalid @enderror" id="mentor_cadangan" name="mentor_cadangan">
                                <option value="">-- Pilih Mentor Cadangan --</option>
                                @foreach ($availableMentors as $mentor)
                                    <option 
                                        value="{{ $mentor->id }}" 
                                        {{ old('mentor_cadangan', $mentorCadanganId) == $mentor->id ? 'selected' : '' }}
                                    >
                                        {{ $mentor->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mentor_cadangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-2">Sinkronkan Penugasan Mentor</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="{{ route('courses.show', $course->id) }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali ke Detail Kursus
        </a>
    </div>
</div>

@endsection