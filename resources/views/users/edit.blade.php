@extends('layouts.users.template')

@section('content')

@php
    // --- Persiapan Data untuk Mengisi Nilai Lama ---
    
    // Default model untuk mencegah error jika relasi belum ada (NULL)
    $profile = $user->profile ?? new \App\Models\Profile(['user_id' => $user->id]); 
    $employeeDetail = $user->employeeDetail ?? new \App\Models\EmployeeDetail(['user_id' => $user->id]);
    $mentorDetail = $user->mentorDetail ?? new \App\Models\MentorDetail(['user_id' => $user->id]);
    $participantDetail = $user->participantDetail ?? new \App\Models\ParticipantDetail(['user_id' => $user->id, 'category' => 'umum']); // Default category 'umum' jika baru
    
    // Untuk PIC Sekolah
    $schoolPivot = $user->schools->first()->pivot ?? null;
    $currentSchoolId = $user->schools->first()->id ?? null;
    
    // --- Konstanta Role ID FINAL ---
    const ROLE_ID_EMPLOYEE = 3; 
    const ROLE_ID_PARTICIPANT = 4; // Peserta
    const ROLE_ID_MENTOR = 5;
    const ROLE_ID_SCHOOL_PIC = 6;
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Manajemen Pengguna /</span> Edit {{ $user->name }}
    </h4>

    {{-- Notifikasi Sukses/Error --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="row">
        <div class="col-lg-10 col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Edit Akses & Detail Pengguna</h5>
                
                {{-- Form HARUS menggunakan enctype untuk upload file (foto profil, ktp, npwp) --}}
                <form id="formEditUser" method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        
                        {{-- BLOK DATA UTAMA (Tabel users) --}}
                        <h6 class="mb-4 text-primary"><i class="bx bx-user me-1"></i> Data Utama & Akses</h6>
                        <div class="row g-3">
                            {{-- Nama Lengkap --}}
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required/>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            {{-- Email --}}
                            <div class="col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required/>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            {{-- Role --}}
                            <div class="col-md-6">
                                <label for="role_id" class="form-label">Role Akses</label>
                                <select id="role_id" name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                    <option value="">Pilih Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" 
                                            {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Password (Opsional) --}}
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password Baru (Kosongkan jika tidak diubah)</label>
                                <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" placeholder="Minimal 8 Karakter"/>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- BLOK DATA SPESIFIK ROLE (Tampil/Sembunyi dengan JS + Blade Awal) --}}
                        <div id="role_specific_details">
                            
                            {{-- DETAIL KARYAWAN (ROLE ID: 3) --}}
                            <div id="employee_fields" 
                                 class="role-fields" 
                                 style="display: {{ $user->role_id == ROLE_ID_EMPLOYEE ? 'block' : 'none' }};">
                                 
                                <h6 class="mb-4 text-primary"><i class="bx bx-briefcase me-1"></i> Detail Karyawan</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="employee_id" class="form-label">Nomor ID Karyawan</label>
                                        <input type="text" id="employee_id" name="employee_id" class="form-control @error('employee_id') is-invalid @enderror" value="{{ old('employee_id', $employeeDetail->employee_id) }}"/>
                                        @error('employee_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="division" class="form-label">Divisi</label>
                                        <input type="text" id="division" name="division" class="form-control @error('division') is-invalid @enderror" value="{{ old('division', $employeeDetail->division) }}"/>
                                        @error('division') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="hire_date" class="form-label">Tanggal Masuk</label>
                                        <input type="date" id="hire_date" name="hire_date" class="form-control @error('hire_date') is-invalid @enderror" value="{{ old('hire_date', $employeeDetail->hire_date) }}"/>
                                        @error('hire_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="emergency_contact" class="form-label">Kontak Darurat</label>
                                        <input type="text" id="emergency_contact" name="emergency_contact" class="form-control @error('emergency_contact') is-invalid @enderror" value="{{ old('emergency_contact', $employeeDetail->emergency_contact) }}"/>
                                        @error('emergency_contact') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <hr class="my-4">
                            </div>

                            {{-- DETAIL PESERTA (ROLE ID: 4) --}}
                            <div id="participant_fields" 
                                 class="role-fields" 
                                 style="display: {{ $user->role_id == ROLE_ID_PARTICIPANT ? 'block' : 'none' }};">
                                 
                                <h6 class="mb-4 text-primary"><i class="bx bx-run me-1"></i> Detail Peserta</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="participant_category" class="form-label">Kategori Peserta</label>
                                        <select id="participant_category" name="participant_category" class="form-select @error('participant_category') is-invalid @enderror">
                                            <option value="">Pilih Kategori</option>
                                            <option value="siswa" {{ old('participant_category', $participantDetail->category) == 'siswa' ? 'selected' : '' }}>Siswa/Pelajar</option>
                                            <option value="mahasiswa" {{ old('participant_category', $participantDetail->category) == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                            <option value="umum" {{ old('participant_category', $participantDetail->category) == 'umum' ? 'selected' : '' }}>Umum</option>
                                        </select>
                                        @error('participant_category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    
                                    {{-- Field Spesifik Siswa --}}
                                    <div class="col-md-6" id="nisn_field" style="display: {{ old('participant_category', $participantDetail->category) == 'siswa' ? 'block' : 'none' }};">
                                        <label for="nisn" class="form-label">NISN/Nomor Induk Siswa</label>
                                        <input type="text" id="nisn" name="nisn" class="form-control @error('nisn') is-invalid @enderror" value="{{ old('nisn', $participantDetail->nisn) }}"/>
                                        @error('nisn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- Field Institusi (Siswa/Mahasiswa) --}}
                                    <div class="col-md-6" id="institution_field" style="display: {{ in_array(old('participant_category', $participantDetail->category), ['siswa', 'mahasiswa']) ? 'block' : 'none' }};">
                                        <label for="institution_name" class="form-label">Nama Sekolah/Kampus/Instansi</label>
                                        <input type="text" id="institution_name" name="institution_name" class="form-control @error('institution_name') is-invalid @enderror" value="{{ old('institution_name', $participantDetail->institution_name) }}"/>
                                        @error('institution_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- Field Spesifik Mahasiswa --}}
                                    <div class="col-md-6" id="major_field" style="display: {{ old('participant_category', $participantDetail->category) == 'mahasiswa' ? 'block' : 'none' }};">
                                        <label for="major" class="form-label">Jurusan/Program Studi</label>
                                        <input type="text" id="major" name="major" class="form-control @error('major') is-invalid @enderror" value="{{ old('major', $participantDetail->major) }}"/>
                                        @error('major') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    
                                </div>
                                <hr class="my-4">
                            </div>
                            
                            {{-- DETAIL MENTOR (ROLE ID: 5) --}}
                            <div id="mentor_fields" 
                                 class="role-fields" 
                                 style="display: {{ $user->role_id == ROLE_ID_MENTOR ? 'block' : 'none' }};">
                                 
                                <h6 class="mb-4 text-primary"><i class="bx bx-book-content me-1"></i> Detail Keuangan & Identitas Mentor</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="ktp_number" class="form-label">Nomor KTP</label>
                                        <input type="text" id="ktp_number" name="ktp_number" class="form-control @error('ktp_number') is-invalid @enderror" value="{{ old('ktp_number', $mentorDetail->ktp_number) }}"/>
                                        @error('ktp_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="npwp_number" class="form-label">Nomor NPWP</label>
                                        <input type="text" id="npwp_number" name="npwp_number" class="form-control @error('npwp_number') is-invalid @enderror" value="{{ old('npwp_number', $mentorDetail->npwp_number) }}"/>
                                        @error('npwp_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    
                                    {{-- Data Bank --}}
                                    <div class="col-md-12">
                                        <h6 class="mt-3 mb-2 text-muted">Data Bank</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="bank_name" class="form-label">Nama Bank</label>
                                        <input type="text" id="bank_name" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror" value="{{ old('bank_name', $mentorDetail->bank_name) }}"/>
                                        @error('bank_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="account_number" class="form-label">Nomor Rekening</label>
                                        <input type="text" id="account_number" name="account_number" class="form-control @error('account_number') is-invalid @enderror" value="{{ old('account_number', $mentorDetail->account_number) }}"/>
                                        @error('account_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="account_holder" class="form-label">Nama Pemilik Rekening</label>
                                        <input type="text" id="account_holder" name="account_holder" class="form-control @error('account_holder') is-invalid @enderror" value="{{ old('account_holder', $mentorDetail->account_holder) }}"/>
                                        @error('account_holder') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    
                                    {{-- File Upload --}}
                                    <div class="col-md-6">
                                        <label for="ktp_file" class="form-label">Upload File KTP</label>
                                        <input class="form-control @error('ktp_file') is-invalid @enderror" type="file" id="ktp_file" name="ktp_file" accept=".pdf,image/*"/>
                                        @error('ktp_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        @if($mentorDetail->ktp_file_path) <small class="text-muted">File tersimpan: <a href="{{ asset('storage/' . $mentorDetail->ktp_file_path) }}" target="_blank">Lihat</a></small> @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label for="npwp_file" class="form-label">Upload File NPWP</label>
                                        <input class="form-control @error('npwp_file') is-invalid @enderror" type="file" id="npwp_file" name="npwp_file" accept=".pdf,image/*"/>
                                        @error('npwp_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        @if($mentorDetail->npwp_file_path) <small class="text-muted">File tersimpan: <a href="{{ asset('storage/' . $mentorDetail->npwp_file_path) }}" target="_blank">Lihat</a></small> @endif
                                    </div>
                                </div>
                                <hr class="my-4">
                            </div>

                            {{-- DETAIL PIC SEKOLAH (ROLE ID: 6) --}}
                            <div id="school_pic_fields" 
                                 class="role-fields" 
                                 style="display: {{ $user->role_id == ROLE_ID_SCHOOL_PIC ? 'block' : 'none' }};">
                                 
                                <h6 class="mb-4 text-primary"><i class="bx bx-building-house me-1"></i> Penugasan Sekolah</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="school_id" class="form-label">Sekolah yang Ditugaskan</label>
                                        <select id="school_id" name="school_id" class="form-select @error('school_id') is-invalid @enderror">
                                            <option value="">Pilih Sekolah</option>
                                            @foreach($schools as $school)
                                                <option value="{{ $school->id }}" 
                                                    {{ old('school_id', $currentSchoolId) == $school->id ? 'selected' : '' }}>
                                                    {{ $school->school_name }} (NPSN: {{ $school->npsn }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('school_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="position" class="form-label">Jabatan di Sekolah</label>
                                        <input type="text" id="position" name="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position', $schoolPivot->position ?? '') }}" placeholder="Contoh: Kepala Sekolah, Koordinator"/>
                                        @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <hr class="my-4">
                            </div>

                        </div> {{-- Penutup role_specific_details --}}

                        {{-- BLOK DATA PROFIL (Tabel user_profiles) --}}
                        <h6 class="mb-4 text-primary"><i class="bx bx-id-card me-1"></i> Data Biodata Umum</h6>
                        <div class="row g-3">
                            {{-- Nomor Telepon --}}
                            <div class="col-md-6">
                                <label class="form-label" for="phone_number">Nomor Telepon</label>
                                <input type="text" id="phone_number" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" placeholder="08123456789" value="{{ old('phone_number', $profile->phone_number) }}"/>
                                @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            {{-- Tempat Lahir --}}
                            <div class="col-md-6">
                                <label for="birth_place" class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control @error('birth_place') is-invalid @enderror" id="birth_place" name="birth_place" placeholder="Jakarta" value="{{ old('birth_place', $profile->birth_place) }}" />
                                @error('birth_place') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            {{-- Tanggal Lahir --}}
                            <div class="col-md-6">
                                <label for="birth_date" class="form-label">Tanggal Lahir</label>
                                <input class="form-control @error('birth_date') is-invalid @enderror" type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $profile->birth_date) }}"/>
                                @error('birth_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Foto Profil (Opsional) --}}
                            <div class="col-md-6">
                                <label for="profile_photo" class="form-label">Foto Profil (Kosongkan jika tidak diubah)</label>
                                <input class="form-control @error('profile_photo') is-invalid @enderror" type="file" id="profile_photo" name="profile_photo" accept="image/png, image/jpeg"/>
                                @error('profile_photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @if($profile->profile_photo_path)
                                    <small class="text-muted d-block">Saat ini menggunakan foto tersimpan.</small>
                                @endif
                            </div>
                            
                            {{-- Alamat --}}
                            <div class="col-md-12">
                                <label for="address" class="form-label">Alamat Rumah</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" placeholder="Jl. Contoh No. 12">{{ old('address', $profile->address) }}</textarea>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role_id');
        const categorySelect = document.getElementById('participant_category');
        
        // Konstanta Role ID
        const ROLE_ID_EMPLOYEE = 3; 
        const ROLE_ID_PARTICIPANT = 4;
        const ROLE_ID_MENTOR = 5;
        const ROLE_ID_SCHOOL_PIC = 6;

        // Blok Detail Role
        const employeeFields = document.getElementById('employee_fields');
        const participantFields = document.getElementById('participant_fields'); 
        const mentorFields = document.getElementById('mentor_fields');
        const schoolPicFields = document.getElementById('school_pic_fields');

        // Field Spesifik Peserta
        const nisnField = document.getElementById('nisn_field');
        const institutionField = document.getElementById('institution_field');
        const majorField = document.getElementById('major_field');


        // FUNGSI UTAMA: Mengatur tampilan Detail Spesifik Role
        function toggleRoleFields() {
            const selectedRoleId = parseInt(roleSelect.value);
            
            // Sembunyikan semua field spesifik
            document.querySelectorAll('.role-fields').forEach(field => {
                field.style.display = 'none';
            });
            
            // Tampilkan field yang sesuai
            if (selectedRoleId === ROLE_ID_EMPLOYEE) {
                employeeFields.style.display = 'block';
            } else if (selectedRoleId === ROLE_ID_PARTICIPANT) {
                participantFields.style.display = 'block';
                // Panggil fungsi toggle kategori Peserta saat blok Peserta muncul
                toggleParticipantCategoryFields();
            } else if (selectedRoleId === ROLE_ID_MENTOR) {
                mentorFields.style.display = 'block';
            } else if (selectedRoleId === ROLE_ID_SCHOOL_PIC) {
                schoolPicFields.style.display = 'block';
            }
        }
        
        // FUNGSI TAMBAHAN: Mengatur tampilan Detail Kategori Peserta
        function toggleParticipantCategoryFields() {
            const selectedCategory = categorySelect.value;
            
            // Sembunyikan semua field kategori
            nisnField.style.display = 'none';
            institutionField.style.display = 'none';
            majorField.style.display = 'none';
            
            if (selectedCategory === 'siswa') {
                nisnField.style.display = 'block';
                institutionField.style.display = 'block'; // Siswa punya Sekolah
            } else if (selectedCategory === 'mahasiswa') {
                institutionField.style.display = 'block'; // Mahasiswa punya Kampus
                majorField.style.display = 'block';
            } else if (selectedCategory === 'umum') {
                // Umum tidak menampilkan detail spesifik tambahan
            }
        }


        // Listener
        roleSelect.addEventListener('change', toggleRoleFields);
        
        // Pastikan categorySelect ada sebelum menambahkan listener
        if (categorySelect) {
            categorySelect.addEventListener('change', toggleParticipantCategoryFields);
        }

        // Panggil fungsi toggle kategori Peserta saat dimuat (Hanya jika role-nya sudah Peserta)
        if (parseInt(roleSelect.value) === ROLE_ID_PARTICIPANT) {
            toggleParticipantCategoryFields();
        }
    });
</script>
@endsection