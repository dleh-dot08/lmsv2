@extends('layouts.users.template')

@section('content')

@php
    use App\Models\User;
    // Ambil Role ID Super Admin
    $superAdminId = User::ID_SUPER_ADMIN; 
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Manajemen /</span> Tambah Pengguna Baru
    </h4>

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

                        {{-- Role --}}
                        <div class="mb-4">
                            <label class="form-label" for="role_id">Pilih Peran (Role)</label>
                            <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Peran --</option>
                                {{-- Variabel $roles dikirim dari UserController::create() --}}
                                @foreach ($roles as $role)
                                    {{-- FILTER: HANYA TAMPILKAN ROLE YANG BUKAN SUPER ADMIN --}}
                                    @if ($role->id != $superAdminId)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection