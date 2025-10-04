@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Master Data /</span> Data Sekolah
    </h4>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Sekolah (Total: {{ $schools->total() }})</h5>
            <a href="{{ route('schools.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Tambah Sekolah
            </a>
        </div>
        
        {{-- Search Form --}}
        <div class="card-body">
            <form method="GET" action="{{ route('schools.index') }}" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Cari Nama Sekolah atau NPSN..." value="{{ request('search') }}">
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-outline-primary"><i class="bx bx-search"></i> Cari</button>
                    <a href="{{ route('schools.index') }}" class="btn btn-outline-secondary"><i class="bx bx-refresh"></i> Reset</a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th>Nama Sekolah (NPSN)</th>
                        <th>Jenjang</th>
                        <th>Kepala Sekolah</th>
                        <th>PIC (Penanggung Jawab)</th>
                        <th style="width: 20%;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($schools as $index => $school)
                        <tr>
                            <td>{{ $schools->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $school->school_name }}</strong> <br>
                                <small class="text-muted">NPSN: {{ $school->npsn }}</small>
                            </td>
                            <td>
                                <strong>{{ $school->level->name ?? 'N/A' }}</strong>
                            </td>
                            <td>{{ $school->headmaster_name }}</td>
                            <td>
                                @php $pic = $school->pics->first(); @endphp
                                @if ($pic)
                                    <strong>{{ $pic->name }}</strong><br>
                                    <small class="text-info">{{ $pic->pivot->position }}</small>
                                @else
                                    <span class="badge bg-label-danger">Belum Ditugaskan</span>
                                @endif
                            </td>
                            <td>
                                {{-- REVISI: Ganti link show untuk melihat daftar peserta --}}
                                <a href="{{ route('schools.show', $school->id) }}" class="btn btn-sm btn-primary" title="Lihat Daftar Peserta">
                                    <i class="bx bx-list-ul me-1"></i> Peserta Didik ({{ $school->students_count ?? '0' }})
                                </a>
                                <a href="{{ route('schools.show', $school->id) }}" class="btn btn-sm btn-info" title="Lihat Detail"><i class="bx bx-detail"></i></a>
                                <a href="{{ route('schools.edit', $school->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="bx bx-edit-alt"></i></a>
                                
                                <form action="{{ route('schools.destroy', $school->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus sekolah {{ $school->school_name }}? Ini juga akan melepaskan PIC terkait.')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="bx bx-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data sekolah yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="card-footer clearfix">
            {{ $schools->links() }}
        </div>
    </div>
</div>

@endsection