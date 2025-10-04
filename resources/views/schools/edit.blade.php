@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Master Data / Data Sekolah /</span> Edit {{ $school->school_name }}
    </h4>

    <div class="card mb-4">
        <h5 class="card-header">Form Edit Sekolah</h5>
        <div class="card-body">
            <form method="POST" action="{{ route('schools.update', $school->id) }}">
                @csrf
                @method('PUT')
                
                <h6 class="mb-4 text-primary"><i class="bx bx-building-house me-1"></i> Data Sekolah</h6>
                <div class="row g-3">
                    {{-- Nama Sekolah --}}
                    <div class="col-md-6">
                        <label for="school_name" class="form-label">Nama Sekolah</label>
                        <input type="text" class="form-control @error('school_name') is-invalid @enderror" id="school_name" name="school_name" value="{{ old('school_name', $school->school_name) }}" required>
                        @error('school_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    {{-- NPSN --}}
                    <div class="col-md-6">
                        <label for="npsn" class="form-label">NPSN</label>
                        <input type="text" class="form-control @error('npsn') is-invalid @enderror" id="npsn" name="npsn" value="{{ old('npsn', $school->npsn) }}" required>
                        @error('npsn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    {{-- Jenjang --}}
                    <div class="col-md-6">
                        <label for="level_id" class="form-label">Jenjang Pendidikan</label>
                        <select class="form-select @error('level_id') is-invalid @enderror" id="level_id" name="level_id" required>
                            <option value="">Pilih Jenjang</option>
                            @foreach ($levels as $level)
                                <option value="{{ $level->id }}" {{ old('level_id', $school->level_id) == $level->id ? 'selected' : '' }}>
                                    {{ $level->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('level_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    {{-- Nama Kepala Sekolah --}}
                    <div class="col-md-6">
                        <label for="headmaster_name" class="form-label">Nama Kepala Sekolah</label>
                        <input type="text" class="form-control @error('headmaster_name') is-invalid @enderror" id="headmaster_name" name="headmaster_name" value="{{ old('headmaster_name', $school->headmaster_name) }}" required>
                        @error('headmaster_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Kota --}}
                    <div class="col-md-6">
                        <label for="city" class="form-label">Kota/Kabupaten</label>
                        <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $school->city) }}">
                        @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Alamat --}}
                    <div class="col-md-12">
                        <label for="full_address" class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control @error('full_address') is-invalid @enderror" id="full_address" name="full_address" rows="3">{{ old('full_address', $school->full_address) }}</textarea>
                        @error('full_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="mb-4 text-primary"><i class="bx bx-user-check me-1"></i> Penugasan PIC Sekolah</h6>
                <div class="row g-3">
                    {{-- Pilih PIC --}}
                    <div class="col-md-6">
                        <label for="pic_user_id" class="form-label">Pilih PIC (Pengguna dengan Role PIC Sekolah)</label>
                        <select class="form-select @error('pic_user_id') is-invalid @enderror" id="pic_user_id" name="pic_user_id" required>
                            <option value="">Pilih User PIC</option>
                            @foreach ($availablePics as $pic)
                                <option value="{{ $pic->id }}" 
                                    {{ old('pic_user_id', $currentPic->id ?? '') == $pic->id ? 'selected' : '' }}>
                                    {{ $pic->name }} ({{ $pic->email }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Daftar termasuk PIC saat ini dan PIC yang belum ditugaskan.</small>
                        @error('pic_user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Jabatan PIC --}}
                    <div class="col-md-6">
                        <label for="pic_position" class="form-label">Jabatan PIC di Sekolah</label>
                        <input type="text" class="form-control @error('pic_position') is-invalid @enderror" id="pic_position" name="pic_position" value="{{ old('pic_position', $currentPic->pivot->position ?? '') }}" required placeholder="Contoh: Koordinator PPDB / Staff Kurikulum">
                        @error('pic_position') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>


                <div class="mt-4">
                    <button type="submit" class="btn btn-primary me-2">Update Sekolah</button>
                    <a href="{{ route('schools.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection