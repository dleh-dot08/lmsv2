@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Program /</span> Tambah Program Baru
    </h4>

    <div class="card">
        <h5 class="card-header">Formulir Tambah Program</h5>
        <div class="card-body">
            
            {{-- Form untuk menyimpan data baru. Arahkan ke route programs.store --}}
            <form method="POST" action="{{ route('programs.store') }}">
                @csrf
                
                {{-- Nama Program --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Program <span class="text-danger">*</span></label>
                    <input 
                        type="text" 
                        class="form-control @error('name') is-invalid @enderror" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}" 
                        placeholder="Contoh: Eskrea, Intra, Extra"
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Deskripsi Program --}}
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi Program</label>
                    <textarea 
                        class="form-control @error('description') is-invalid @enderror" 
                        id="description" 
                        name="description" 
                        rows="3" 
                        placeholder="Deskripsi singkat tentang program ini."
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status Aktif --}}
                <div class="mb-3 form-check">
                    <input 
                        type="checkbox" 
                        class="form-check-input" 
                        id="is_active" 
                        name="is_active" 
                        value="1" 
                        checked 
                    >
                    <label class="form-check-label" for="is_active">Aktif (Program dapat dipilih/digunakan)</label>
                    @error('is_active')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                
                <hr>

                {{-- Tombol Aksi --}}
                <a href="{{ route('programs.index') }}" class="btn btn-secondary me-2">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    Simpan Program
                </button>
            </form>
        </div>
    </div>
</div>

@endsection