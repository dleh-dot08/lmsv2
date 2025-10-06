@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Kategori /</span> Edit Kategori: **{{ $category->name }}**
    </h4>

    <div class="card">
        <h5 class="card-header">Formulir Edit Kategori</h5>
        <div class="card-body">
            
            <form method="POST" action="{{ route('categories.update', $category->id) }}">
                @csrf
                @method('PUT') 
                
                {{-- Nama Kategori --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                    <input 
                        type="text" 
                        class="form-control @error('name') is-invalid @enderror" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $category->name) }}" 
                        placeholder="Contoh: Seni, Olahraga"
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
                        @foreach ($parentCategories as $parent)
                            <option 
                                value="{{ $parent->id }}" 
                                {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}
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
                        value="{{ old('type', $category->type) }}" 
                        placeholder="Contoh: eskrea, news, resource"
                    >
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
                    >{{ old('description', $category->description) }}</textarea>
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
                    Update Kategori
                </button>
            </form>
        </div>
    </div>
</div>

@endsection