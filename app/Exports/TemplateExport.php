<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TemplateExport implements FromCollection, WithHeadings
{
    protected $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Data Dummy untuk tipe 'create_assign'
        if ($this->type === 'create_assign') {
            return new Collection([
                [
                    'nisn' => '1234567890', 
                    'npsn' => '20500000', // Ganti dengan NPSN sekolah yang valid
                    'name' => 'Budi Santoso', 
                    'email' => 'budi@contoh.sch.id', 
                    'password' => '123456', 
                    'initial_grade_name' => 'Kelas 10', // Pastikan sesuai dengan nama di tabel grades
                    'category' => 'siswa', 
                    'major' => 'IPA',
                ],
                [
                    'nisn' => '1234567891', 
                    'npsn' => '20500001', 
                    'name' => 'Siti Aisyah', 
                    'email' => 'siti@contoh.sch.id', 
                    'password' => '', // Jika dikosongkan, akan menggunakan NISN atau password default
                    'initial_grade_name' => 'Kelas 10', 
                    'category' => 'siswa', 
                    'major' => 'IPS',
                ]
            ]);
        }

        // Data Dummy untuk tipe 'grade_update' (Kenaikan Kelas)
        if ($this->type === 'grade_update') {
            return new Collection([
                [
                    'nisn' => '1234567890', // NISN peserta yang sudah ada di database
                    'target_grade_name' => 'Kelas 11', // Nama Kelas Baru (Pastikan ada di tabel grades)
                ],
                [
                    'nisn' => '1234567891', 
                    'target_grade_name' => 'Kelas 11', 
                ]
            ]);
        }

        // Default: kembalikan koleksi kosong
        return new Collection([]);
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        // Logika headings tetap sama
        if ($this->type === 'create_assign') {
            return [
                'nisn', 
                'npsn', 
                'name', 
                'email', 
                'password', 
                'initial_grade_name', 
                'category', 
                'major', 
            ];
        }

        if ($this->type === 'grade_update') {
            return [
                'nisn', 
                'target_grade_name',
            ];
        }

        return [];
    }
}