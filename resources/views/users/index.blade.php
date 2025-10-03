@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Manajemen /</span> Daftar Pengguna
    </h4>

    {{-- Notifikasi Sukses/Error --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Semua Pengguna ({{ $users->total() }})</h5>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah Pengguna Baru
                </a>
            </div>
        </div>

        {{-- SEARCH & FILTER SECTION --}}
        <div class="card-body">
            <form action="{{ route('users.index') }}" method="GET" id="filterForm">
                <div class="row g-3">
                    {{-- Pencarian --}}
                    <div class="col-md-6 col-lg-8">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama atau email pengguna..." value="{{ request('search') }}">
                        </div>
                    </div>
                    
                    {{-- Filter Role --}}
                    <div class="col-md-4 col-lg-3">
                        <select name="role_id" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">-- Semua Role --</option>
                            {{-- $roles harus dikirim dari UserController::index() --}}
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" 
                                    {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tombol Search/Filter --}}
                    <div class="col-md-2 col-lg-1">
                        <button type="submit" class="btn btn-secondary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
        
        {{-- TABLE SECTION (Menggunakan layout yang responsif) --}}
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status Profil</th>
                        <th>Terakhir Diperbarui</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($users as $user)
                    <tr>
                        <td>{{ $users->firstItem() + $loop->index }}</td>
                        <td><i class="bx bx-user me-1"></i> <strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-label-info me-1">{{ $user->role->name ?? 'N/A' }}</span>
                        </td>
                        <td>
                            {{-- Cek apakah data user_profiles sudah diisi (asumsi: phone_number sebagai indikator kelengkapan) --}}
                            @if ($user->profile && $user->profile->phone_number)
                                <span class="badge bg-label-success">Lengkap</span>
                            @else
                                <span class="badge bg-label-warning">Belum Lengkap</span>
                            @endif
                        </td>
                        <td>{{ $user->updated_at->format('d M Y') }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    
                                    {{-- TOMBOL VIEW (users.show) --}}
                                    <a class="dropdown-item" href="{{ route('users.show', $user->id) }}">
                                        <i class="bx bx-show me-1"></i> Lihat Detail
                                    </a>

                                    {{-- TOMBOL EDIT (users.edit) --}}
                                    <a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">
                                        <i class="bx bx-edit-alt me-1"></i> Edit Detail
                                    </a>
                                    
                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus pengguna ini? Semua data terkait akan ikut terhapus!');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bx bx-trash me-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data pengguna ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="card-footer">
            {{ $users->links() }}
        </div>
    </div>
</div>

@endsection