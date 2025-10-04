@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Master Data / Kelas /</span> Edit {{ $grade->name }}
    </h4>

    <div class="card mb-4">
        <h5 class="card-header">Form Edit Kelas/Tingkatan</h5>
        <div class="card-body">
            <form method="POST" action="{{ route('grades.update', $grade->id) }}">
                @csrf
                @method('PUT')
                
                {{-- Pilih Jenjang --}}
                <div class="mb-3">
                    <label for="level_id" class="form-label">Jenjang Pendidikan</label>
                    <select class="form-select @error('level_id') is-invalid @enderror" id="level_id" name="level_id" required>
                        <option value="">Pilih Jenjang</option>
                        @foreach ($levels as $level)
                            <option value="{{ $level->id }}" {{ old('level_id', $grade->level_id) == $level->id ? 'selected' : '' }}>
                                {{ $level->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('level_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Nama Kelas --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Kelas (Contoh: Kelas 1, Semester 1)</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $grade->name) }}" required>
                    <small class="text-muted">Nama kelas harus unik dalam satu jenjang.</small>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                {{-- Urutan --}}
                <div class="mb-3">
                    <label for="order" class="form-label">Nomor Urut (Untuk Sorting)</label>
                    <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $grade->order) }}">
                    <small class="text-muted">Contoh: Kelas 1 diberi urutan 1, Kelas 2 diberi urutan 2.</small>
                    @error('order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary me-2">Update Kelas</button>
                    <a href="{{ route('grades.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection