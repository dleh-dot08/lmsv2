@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Master Data /</span> Kelas & Tingkatan
    </h4>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Kelas (Total: {{ $grades->total() }})</h5>
            <a href="{{ route('grades.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Tambah Kelas
            </a>
        </div>
        
        {{-- Filter & Search Form --}}
        <div class="card-body">
            <form method="GET" action="{{ route('grades.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="level_id" class="form-label">Filter Jenjang</label>
                    <select name="level_id" id="level_id" class="form-select">
                        <option value="">-- Semua Jenjang --</option>
                        @foreach ($levels as $level)
                            <option value="{{ $level->id }}" {{ request('level_id') == $level->id ? 'selected' : '' }}>
                                {{ $level->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Cari Nama Kelas</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Cari berdasarkan Nama Kelas..." value="{{ request('search') }}">
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-outline-primary"><i class="bx bx-search"></i> Filter & Cari</button>
                    <a href="{{ route('grades.index') }}" class="btn btn-outline-secondary"><i class="bx bx-refresh"></i> Reset</a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th>Kelas / Tingkatan</th>
                        <th>Jenjang</th>
                        <th>Urutan</th>
                        <th style="width: 20%;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($grades as $index => $grade)
                        <tr>
                            <td>{{ $grades->firstItem() + $index }}</td>
                            <td><strong>{{ $grade->name }}</strong></td>
                            <td>{{ $grade->level->name ?? 'Jenjang Tidak Ditemukan' }}</td>
                            <td>{{ $grade->order }}</td>
                            <td>
                                {{-- <a href="{{ route('grades.show', $grade->id) }}" class="btn btn-sm btn-info" title="Lihat Detail"><i class="bx bx-detail"></i></a> --}}
                                <a href="{{ route('grades.edit', $grade->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="bx bx-edit-alt"></i></a>
                                
                                <form action="{{ route('grades.destroy', $grade->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kelas {{ $grade->name }}?')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="bx bx-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data kelas yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="card-footer clearfix">
            {{ $grades->links() }}
        </div>
    </div>
</div>

@endsection