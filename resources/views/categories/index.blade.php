@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Manajemen Konten /</span> Daftar Kategori
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
            <h5 class="m-0">Data Kategori</h5>
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Tambah Kategori Baru
            </a>
        </div>
        
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Kategori</th>
                        <th>Slug</th>
                        <th>Tipe</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($categories as $category)
                        {{-- KATEGORI INDUK --}}
                        <tr class="table-primary">
                            <td>{{ $loop->iteration }}</td>
                            <td><i class="bx bx-folder me-2"></i> <strong>{{ $category->name }}</strong></td>
                            <td>{{ $category->slug }}</td>
                            <td><span class="badge bg-label-info">{{ $category->type ?? '-' }}</span></td>
                            <td>{{ Str::limit($category->description, 50) }}</td>
                            <td>
                                @include('categories.partials.action_dropdown', ['category' => $category])
                            </td>
                        </tr>

                        {{-- SUB-KATEGORI (CHILDREN) --}}
                        @foreach ($category->children as $child)
                            <tr>
                                <td></td>
                                <td style="padding-left: 30px;"><i class="bx bx-file-blank me-2"></i> &mdash; {{ $child->name }}</td>
                                <td>{{ $child->slug }}</td>
                                <td><span class="badge bg-label-secondary">{{ $child->type ?? '-' }}</span></td>
                                <td>{{ Str::limit($child->description, 50) }}</td>
                                <td>
                                    @include('categories.partials.action_dropdown', ['category' => $child])
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                Belum ada data Kategori Induk yang tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Memisahkan Dropdown Aksi agar lebih rapi --}}
@include('categories.partials.action_dropdown')

@endsection

@php
// Partial View: resources/views/categories/partials/action_dropdown.blade.php
// (Anda perlu membuat file ini)

// Tujuannya agar kode di atas lebih bersih.
@endphp