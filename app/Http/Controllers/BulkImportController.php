<?php

namespace App\Http\Controllers;

use App\Imports\StagingImport;
use App\Models\BulkImportStaging;
use App\Services\BulkDataValidator;
use App\Services\BulkDataProcessor;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Exports\TemplateExport;

class BulkImportController extends Controller
{
    /**
     * Menampilkan form upload dan riwayat import.
     */
    public function showImportForm()
    {
        // Mengambil riwayat 10 import terbaru (unique per batch_token)
        // Kita menggunakan grouping untuk memastikan setiap batch hanya terwakili satu kali di riwayat
        $recentImports = BulkImportStaging::select(
            'batch_token', 
            'import_type', 
            'uploaded_by', 
            DB::raw('MAX(created_at) as created_at') // Ambil timestamp terbaru dari batch
        )
        ->groupBy('batch_token', 'import_type', 'uploaded_by') // Grouping berdasarkan key batch
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
                            
        return view('students.import_form', compact('recentImports'));
    }

    public function downloadTemplate(Request $request, $type)
    {
        // 1. Validasi tipe yang diminta
        if (!in_array($type, ['create_assign', 'grade_update'])) {
            // Jika tipe tidak valid, kembalikan error 404
            abort(404, 'Tipe template impor tidak valid.');
        }

        // 2. Tentukan nama file
        $filename = "template_import_" . $type . "_" . date('Ymd') . ".xlsx";
        
        // 3. Gunakan TemplateExport yang sudah kita buat
        // TemplateExport akan menentukan header berdasarkan variabel $type
        return Excel::download(new TemplateExport($type), $filename);
    }

    /**
     * Menerima file, menyimpannya ke Staging, dan memicu Validasi.
     */
    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'import_type' => 'required|in:create_assign,grade_update',
        ]);
        
        $batchToken = (string) Str::uuid();
        // Pastikan Anda mendapatkan ID pengguna yang sedang login
        $uploadedBy = auth()->id() ?? 1; 

        try {
            // 1. Simpan data mentah ke tabel Staging
            Excel::import(new StagingImport($uploadedBy, $request->import_type, $batchToken), $request->file('file'));

            // 2. Lakukan Validasi data di Staging
            $validator = new BulkDataValidator($batchToken, $request->import_type);
            $validator->validateStagingData();

            // Redirect ke halaman review/validasi
            return redirect()->route('students.review', $batchToken)
                             ->with('success', 'File berhasil diunggah dan validasi awal sedang ditampilkan.');

        } catch (\Exception $e) {
            // Hapus data staging jika ada error fatal saat upload
            BulkImportStaging::where('batch_token', $batchToken)->delete();
            return back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }
    
    /**
     * Final Review (Tampilkan data staging yang success/failed)
     */
    public function reviewStaging($batchToken)
    {
        $stagedData = BulkImportStaging::where('batch_token', $batchToken)->get();
        // Cek jika tidak ada data
        if ($stagedData->isEmpty()) {
            return redirect()->route('students.show_import')->with('error', 'Data import tidak ditemukan atau sudah diproses.');
        }

        $successCount = $stagedData->where('validation_status', 'success')->count();
        $failedCount = $stagedData->where('validation_status', 'failed')->count();
        
        // Variabel $batchToken didefinisikan di sini untuk dikirim ke view students.review
        return view('students.review', compact('stagedData', 'batchToken', 'successCount', 'failedCount'));
    }

    /**
     * Final Commit (Simpan data yang LULUS VALIDASI ke tabel utama)
     */
    public function commitStaging($batchToken)
    {
        $stagedData = BulkImportStaging::where('batch_token', $batchToken)
                                       ->where('validation_status', 'success')
                                       ->get();
        
        if ($stagedData->isEmpty()) {
            return back()->with('error', 'Tidak ada data yang valid untuk disimpan.');
        }

        $processor = new BulkDataProcessor(); 
        $result = $processor->processCommit($stagedData);

        // Hapus data Staging setelah diproses
        BulkImportStaging::where('batch_token', $batchToken)->delete();

        return redirect()->route('students.show_import')
                         ->with('success', "Proses commit selesai. Berhasil: {$result['success']} / Gagal: {$result['failed']} (gagal saat commit).");
    }
}
