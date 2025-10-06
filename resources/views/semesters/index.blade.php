@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Manajemen Waktu /</span> Daftar Semester
    </h4>

    {{-- Pesan Notifikasi Sukses --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0">Data Periode Semester</h5>
            <a href="{{ route('semesters.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Tambah Semester Baru
            </a>
        </div>
        
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Semester</th>
                        <th>Tahun Ajaran</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($semesters as $semester)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $semester->name }}</strong></td>
                            <td>{{ $semester->academic_year }}</td>
                            <td>
                                {{ $semester->start_date->format('d M Y') }} - 
                                {{ $semester->end_date->format('d M Y') }}
                            </td>
                            <td>
                                @if($semester->is_active)
                                    <span class="badge bg-label-primary me-1">AKTIF SAAT INI</span>
                                @else
                                    <span class="badge bg-label-secondary me-1">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('semesters.edit', $semester->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        
                                        {{-- Form Hapus --}}
                                        <form action="{{ route('semesters.destroy', $semester->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semester {{ $semester->name }}?');">
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
                            <td colspan="6" class="text-center">
                                Belum ada data Semester yang tersedia. Silakan <a href="{{ route('semesters.create') }}">tambahkan</a> yang baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection