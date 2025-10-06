@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Kategori /</span> Tambah Kategori Baru
    </h4>

    <div class="card">
        <h5 class="card-header">Formulir Tambah Kategori</h5>
        <div class="card-body">
            
            {{-- Form untuk menyimpan data baru. Arahkan ke route categories.store --}}
            <form method="POST" action="{{ route('categories.store') }}">
                @csrf
                
                {{-- Nama Kategori --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                    <input 
                        type="text" 
                        class="form-control @error('name') is-invalid @enderror" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}" 
                        placeholder="Contoh: Basket, Seni Tari, Olahraga"
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Kategori Induk (Parent) --}}
                <div class="mb-3">
                    <label for="parent_id" class="form-label">Kategori Induk</label>
                    <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                        <option value="">-- Pilih Induk (Jadikan Kategori Utama) --</option>
                        {{-- Data parentCategories diambil dari Controller --}}
                        @foreach ($parentCategories as $parent)
                            <option 
                                value="{{ $parent->id }}" 
                                {{ old('parent_id') == $parent->id ? 'selected' : '' }}
                            >
                                {{ $parent->name }} ({{ $parent->type ?? 'Umum' }})
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tipe Kategori --}}
                <div class="mb-3">
                    <label for="type" class="form-label">Tipe Kategori (Opsional)</label>
                    <input 
                        type="text" 
                        class="form-control @error('type') is-invalid @enderror" 
                        id="type" 
                        name="type" 
                        value="{{ old('type') }}" 
                        placeholder="Contoh: eskrea, intra, news"
                    >
                    <div class="form-text">
                        Untuk mengelompokkan kategori berdasarkan jenis konten/program (misal: kategori ini hanya berlaku untuk program Eskrea).
                    </div>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Deskripsi Program --}}
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi Kategori</label>
                    <textarea 
                        class="form-control @error('description') is-invalid @enderror" 
                        id="description" 
                        name="description" 
                        rows="3" 
                        placeholder="Deskripsi singkat tentang kategori ini."
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <hr>

                {{-- Tombol Aksi --}}
                <a href="{{ route('categories.index') }}" class="btn btn-secondary me-2">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    Simpan Kategori
                </button>
            </form>
        </div>
    </div>
</div>

@endsection