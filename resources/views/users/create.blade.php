@extends('layouts.users.template') {{-- Sesuaikan dengan layout utama Anda --}}

@section('content')

{{-- ASUMSI KONSTANTA ROLE ID PESERTA (4) --}}
@php
    // Ganti nilai 4 ini dengan konstanta ROLE_ID_PARTICIPANT yang benar dari Model User Anda
    // Jika ROLE_ID_PARTICIPANT di Controller adalah 4, gunakan 4 di sini.
    $roleIdParticipant = 4;
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Manajemen /</span> Tambah Pengguna Baru
    </h4>

    {{-- Notifikasi Error/Success --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            Gagal menyimpan data. Mohon periksa kembali input Anda.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Formulir Pengguna</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        
                        {{-- Nama Lengkap --}}
                        <div class="mb-3">
                            <label class="form-label" for="name">Nama Lengkap</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama lengkap pengguna" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="email@contoh.com" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="mb-3">
                            <label class="form-label" for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="********" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div class="mb-3">
                            <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="********" required>
                        </div>

                        {{-- Role (Pemicu Kondisional) --}}
                        <div class="mb-4">
                            <label class="form-label" for="role_id">Pilih Peran (Role)</label>
                            <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Peran --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- FIELD KATEGORI PESERTA (KONDISIONAL) --}}
                        <div class="mb-4" id="participant-fields" style="display: {{ old('role_id') == $roleIdParticipant || $errors->has('participant_category') ? 'block' : 'none' }};">
                            <label class="form-label" for="participant_category">Kategori Peserta</label>
                            <select name="participant_category" id="participant_category" class="form-select @error('participant_category') is-invalid @enderror">
                                <option value="">-- Pilih Kategori --</option>
                                <option value="siswa" {{ old('participant_category') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                                <option value="mahasiswa" {{ old('participant_category') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                <option value="umum" {{ old('participant_category') == 'umum' ? 'selected' : '' }}>Umum/Lainnya</option>
                            </select>
                            @error('participant_category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- END KONDISIONAL FIELD --}}


                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT UNTUK MENAMPILKAN FIELD KATEGORI PESERTA SECARA KONDISIONAL --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role_id');
        const participantFields = document.getElementById('participant-fields');
        // PASTIKAN participantRoleId SAMA DENGAN self::ROLE_ID_PARTICIPANT di Controller (Misal: 4)
        const participantRoleId = {{ $roleIdParticipant }}; 

        function toggleParticipantFields() {
            if (roleSelect.value == participantRoleId) {
                participantFields.style.display = 'block';
                // Jika ditampilkan, set attribute 'required' jika diperlukan oleh validasi Anda
                // document.getElementById('participant_category').setAttribute('required', 'required');
            } else {
                participantFields.style.display = 'none';
                // Hapus nilai dan attribute 'required' saat disembunyikan
                // document.getElementById('participant_category').removeAttribute('required');
                // document.getElementById('participant_category').value = ''; 
            }
        }

        roleSelect.addEventListener('change', toggleParticipantFields);

        // Panggil saat load halaman untuk menangani kasus old('role_id') dan error validasi
        toggleParticipantFields(); 
    });
</script>
@endpush

@endsection