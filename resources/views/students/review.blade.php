@extends('layouts.users.template') 

@section('content')
<div class="container-fluid">
    <h1>Review Data Import</h1>
    
    {{-- Variabel $batchToken dikirim dari BulkImportController::reviewStaging --}}
    <h3 class="mb-4 text-muted">Batch Token: {{ $batchToken }}</h3>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <div class="alert alert-info shadow-sm">
        Data berhasil diunggah dan divalidasi. Berikut ringkasan dari batch ini: <br>
        ✅ Baris Lulus Validasi: <strong>{{ $successCount }}</strong> <br>
        ❌ Baris Gagal Validasi: <strong>{{ $failedCount }}</strong>
    </div>

    <!-- Tombol Final Commit -->
    @if ($successCount > 0)
        {{-- Tombol commit hanya aktif jika ada data yang valid --}}
        <form action="{{ route('students.commit', $batchToken) }}" method="POST" onsubmit="return confirm('ANDA YAKIN ingin menyimpan {{ $successCount }} baris data yang valid ke database utama? Tindakan ini tidak dapat dibatalkan.');">
            @csrf
            <button type="submit" class="btn btn-success btn-lg mb-4 shadow">
                ✅ Commit Data Valid ({{ $successCount }} Baris)
            </button>
        </form>
    @else
        <div class="alert alert-warning mb-4">Tidak ada data yang lulus validasi untuk disimpan (Commit). Harap periksa detail error di bawah.</div>
    @endif
    
    <hr>

    <h2>Detail Validasi Per Baris</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-sm">
            <thead class="bg-light">
                <tr>
                    <th style="width: 5%">Status</th>
                    <th style="width: 15%">NISN</th>
                    <th style="width: 15%">Nama</th>
                    <th style="width: 15%">Email</th>
                    <th style="width: 10%">NPSN</th>
                    <th style="width: 15%">Kelas Awal / Target</th>
                    <th style="width: 25%">Keterangan Error</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stagedData as $row)
                    <tr>
                        <td>
                            @if ($row->validation_status === 'success')
                                <span class="badge bg-success">SUCCESS</span>
                            @else
                                <span class="badge bg-danger">FAILED</span>
                            @endif
                        </td>
                        <td>{{ $row->nisn }}</td>
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->email }}</td>
                        <td>{{ $row->npsn }}</td>
                        <td>
                            {{-- Tampilkan nama kelas berdasarkan tipe impor --}}
                            {{ $row->initial_grade_name ?? $row->target_grade_name }}
                        </td>
                        <td>
                            @if ($row->validation_errors)
                                {{-- Karena disimpan sebagai JSON, kita decode dan tampilkan --}}
                                @foreach(json_decode($row->validation_errors, true) as $error)
                                    <span class="text-danger small d-block">- {{ $error }}</span>
                                @endforeach
                            @else
                                <span class="text-success small">Valid. Siap di-Commit.</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{ route('students.show_import') }}" class="btn btn-secondary mt-3">Kembali ke Upload</a>
</div>
@endsection
