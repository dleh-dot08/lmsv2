@extends('layouts.users.template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Kelas / {{ $course->nama_kelas }} /</span> Daftar Jadwal Pertemuan
    </h4>

    {{-- Pesan Notifikasi Sukses/Error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0">
                Jadwal Kelas: {{ $course->nama_kelas }} 
                <span class="badge bg-label-primary">{{ $schedules->count() }} / 20 Pertemuan</span>
            </h5>
            <a href="{{ route('courses.schedules.create', $course->id) }}" class="btn btn-primary"
               @if ($schedules->count() >= 20) disabled @endif>
                <i class="bx bx-calendar-plus me-1"></i> Tambah Pertemuan Baru
            </a>
        </div>
        
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Pertemuan Ke</th>
                        <th>Tanggal</th>
                        <th>Waktu Sesi</th>
                        <th>Ruangan/Lokasi</th>
                        <th>Topik Materi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($schedules as $schedule)
                        @php
                            $isPast = \Carbon\Carbon::parse($schedule->tanggal_pertemuan)->isPast();
                        @endphp
                        <tr @if($isPast) class="table-light text-muted" @endif>
                            <td><strong>{{ $schedule->pertemuan_ke }}</strong></td>
                            <td>
                                {{ $schedule->tanggal_pertemuan->format('D, d M Y') }}
                                @if($isPast) <span class="badge bg-secondary ms-1">Selesai</span> @endif
                            </td>
                            <td>
                                {{ substr($schedule->waktu_mulai_sesi, 0, 5) }} - {{ substr($schedule->waktu_akhir_sesi, 0, 5) }}
                            </td>
                            <td>{{ $schedule->ruangan ?? '—' }}</td>
                            <td>{{ $schedule->topik_materi ?? 'Belum Ditentukan' }}</td>
                            <td>
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        {{-- Link EDIT (Sama seperti sebelumnya) --}}
                                        <a class="dropdown-item" href="{{ route('courses.schedules.edit', [$course->id, $schedule->id]) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>

                                        {{-- TOMBOL BARU: Lihat History (Membuka Modal) --}}
                                        {{-- Gunakan data-bs-toggle dan data-schedule-id untuk AJAX atau data loading --}}
                                        <a class="dropdown-item" href="javascript:void(0);" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#historyModal" 
                                            data-schedule-id="{{ $schedule->id }}" 
                                            data-pertemuan-ke="{{ $schedule->pertemuan_ke }}"
                                            onclick="loadScheduleHistory({{ $schedule->id }}, {{ $schedule->pertemuan_ke }})">
                                            <i class="bx bx-history me-1"></i> Riwayat Perubahan
                                        </a>
                                        
                                        <div class="dropdown-divider"></div>

                                        {{-- Form HAPUS (Sama seperti sebelumnya) --}}
                                        <form action="{{ route('courses.schedules.destroy', [$course->id, $schedule->id]) }}" method="POST" onsubmit="return confirm('Yakin menghapus jadwal pertemuan ke-{{ $schedule->pertemuan_ke }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bx bx-trash me-1"></i> Hapus
                                            </button>
                                        </form>
                                    
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                Belum ada Jadwal Pertemuan. Silakan tambahkan pertemuan pertama!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="historyModalLabel">Riwayat Perubahan Pertemuan ke-**<span id="modal-pertemuan-ke"></span>**</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="history-content">
                            <p class="text-center text-muted">Memuat data riwayat...</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar Kursus
            </a>
        </div>
    </div>
</div>

<script>
function loadScheduleHistory(scheduleId, pertemuanKe) {
    const modalTitle = document.getElementById('modal-pertemuan-ke');
    const historyContent = document.getElementById('history-content');
    
    // 1. Tampilkan Judul Modal
    modalTitle.textContent = pertemuanKe;
    historyContent.innerHTML = '<p class="text-center text-muted"><i class="bx bx-loader-alt bx-spin"></i> Memuat data riwayat...</p>';

    // 2. Lakukan AJAX Request untuk mengambil data history
    // Kita akan buat Route/Controller method baru untuk ini: courses.schedules.history
    const historyUrl = `/courses/{{ $course->id }}/schedules/${scheduleId}/history`; 

    fetch(historyUrl)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                historyContent.innerHTML = '<div class="alert alert-info">Belum ada riwayat perubahan yang tercatat.</div>';
                return;
            }

            let html = '<ul class="list-group">';
            
            data.forEach(history => {
                let user = history.user ? `(${history.user.name})` : '';
                
                html += `<li class="list-group-item d-flex justify-content-between align-items-start">`;
                html += `   <div class="ms-2 me-auto">`;
                html += `       <div class="fw-bold">Diubah pada ${new Date(history.changed_at).toLocaleString()} ${user}</div>`;
                html += `       <small class="text-danger">Alasan: ${history.reason}</small><br>`;
                
                // Tampilkan Detail Perubahan (change_summary)
                if (history.change_summary && history.change_summary.length > 0) {
                    html += `       <div class="mt-2 small">`;
                    history.change_summary.forEach(change => {
                        html += `           <span class="badge bg-label-secondary me-2">${change.field}</span>: `;
                        html += `           <span class="text-decoration-line-through text-muted me-1">${change.old_value}</span> &rarr; <strong>${change.new_value}</strong><br>`;
                    });
                    html += `       </div>`;
                } else {
                    html += `       <p class="small text-muted">Detail perubahan tidak tersedia.</p>`;
                }

                html += `   </div>`;
                html += `</li>`;
            });

            html += '</ul>';
            historyContent.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading history:', error);
            historyContent.innerHTML = '<div class="alert alert-warning">Gagal memuat riwayat. Silakan coba lagi.</div>';
        });
}
</script>
@endsection