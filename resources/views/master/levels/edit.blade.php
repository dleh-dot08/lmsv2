@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Master Data / Jenjang /</span> Edit {{ $level->name }}
    </h4>

    <div class="card mb-4">
        <h5 class="card-header">Form Edit Jenjang</h5>
        <div class="card-body">
            <form method="POST" action="{{ route('levels.update', $level->id) }}">
                @csrf
                @method('PUT')
                
                {{-- Nama Jenjang --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Jenjang (Contoh: SD, SMA, Umum)</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $level->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                {{-- Deskripsi --}}
                <div class="mb-3">
                    <label for="description" class="form-label">Keterangan/Deskripsi (Opsional)</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $level->description) }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary me-2">Update Jenjang</button>
                    <a href="{{ route('levels.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection