@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Master Data /</span> Jenjang Pendidikan
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
            <h5 class="mb-0">Daftar Jenjang (Total: {{ $levels->total() }})</h5>
            <a href="{{ route('levels.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Tambah Jenjang
            </a>
        </div>
        
        {{-- Search Form --}}
        <div class="card-body">
            <form method="GET" action="{{ route('levels.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan Nama Jenjang..." value="{{ request('search') }}">
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-outline-primary"><i class="bx bx-search"></i> Cari</button>
                    <a href="{{ route('levels.index') }}" class="btn btn-outline-secondary"><i class="bx bx-refresh"></i> Reset</a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th>Nama Jenjang</th>
                        <th>Keterangan</th>
                        <th style="width: 20%;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($levels as $index => $level)
                        <tr>
                            <td>{{ $levels->firstItem() + $index }}</td>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{ $level->name }}</strong></td>
                            <td>{{ $level->description ?? '-' }}</td>
                            <td>
                                <a href="{{ route('levels.show', $level->id) }}" class="btn btn-sm btn-info" title="Lihat Detail"><i class="bx bx-detail"></i></a>
                                <a href="{{ route('levels.edit', $level->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="bx bx-edit-alt"></i></a>
                                
                                <form action="{{ route('levels.destroy', $level->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus jenjang {{ $level->name }}? Semua Kelas di dalamnya juga akan terhapus.')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="bx bx-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada data jenjang yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="card-footer clearfix">
            {{ $levels->links() }}
        </div>
    </div>
</div>

@endsection