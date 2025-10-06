<?php

namespace App\Imports;

use App\Models\BulkImportStaging;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StagingImport implements ToCollection, WithHeadingRow
{
    protected $uploadedBy;
    protected $importType;
    protected $batchToken;

    public function __construct($uploadedBy, $importType, $batchToken)
    {
        $this->uploadedBy = $uploadedBy;
        $this->importType = $importType;
        $this->batchToken = $batchToken;
    }

    public function collection(Collection $rows)
    {
        $dataToInsert = [];
        
        foreach ($rows as $row) {
            // Mapping data mentah ke tabel Staging
            $dataToInsert[] = [
                'uploaded_by' => $this->uploadedBy,
                'import_type' => $this->importType,
                'batch_token' => $this->batchToken,
                'name' => $row['name'] ?? null,
                'email' => $row['email'] ?? null,
                'nisn' => $row['nisn'] ?? null,
                'npsn' => $row['npsn'] ?? null,
                'category' => $row['category'] ?? null,
                'major' => $row['major'] ?? null,
                'password' => $row['password'] ?? null,
                'initial_grade_name' => $row['initial_grade_name'] ?? null,
                'target_grade_name' => $row['target_grade_name'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert massal ke tabel Staging
        BulkImportStaging::insert($dataToInsert);
    }
}