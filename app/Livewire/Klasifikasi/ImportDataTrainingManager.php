<?php

namespace App\Livewire\Klasifikasi;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\DataTraining;
use App\Models\DataTrainingRkas;
use App\Models\DataTrainingBku;
use App\Imports\DataTrainingRkasImport;
use App\Imports\DataTrainingBkuImport;
use App\Services\PreprocessingPipeline;
use App\Exports\TemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class ImportDataTrainingManager extends Component
{
    use WithFileUploads;

    public $file_rkas = null;
    public $file_bku = null;

    public function mount()
    {
        if (!auth()->user()?->isAdmin()) {
            abort(403, 'Akses ditolak. Hanya Admin/Bendahara yang dapat mengelola data training.');
        }
    }

    public function importRkas()
    {
        $this->validate([
            'file_rkas' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file_rkas.required' => 'Wajib memilih file Excel RKAS.',
            'file_rkas.mimes' => 'Format file harus .xlsx, .xls, atau .csv',
        ]);

        try {
            // Hapus data lama ketika mengunggah file baru (overwrite/timpa)
            DataTrainingRkas::truncate();
            
            Excel::import(new DataTrainingRkasImport(), $this->file_rkas->getRealPath());
            $this->reset('file_rkas');

            // Otomatis jalankan pipeline preprocessing jika data BKU juga tersedia
            if (DataTrainingBku::exists()) {
                (new PreprocessingPipeline())->process();
            }

            session()->flash('success', 'Data RKAS baru berhasil diunggah!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengimpor data RKAS: ' . $e->getMessage());
        }
    }

    public function importBku()
    {
        $this->validate([
            'file_bku' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file_bku.required' => 'Wajib memilih file Excel BKU.',
            'file_bku.mimes' => 'Format file harus .xls, .xlsx, atau .csv',
        ]);

        try {
            // Hapus data lama ketika mengunggah file baru (overwrite/timpa)
            DataTrainingBku::truncate();
            
            Excel::import(new DataTrainingBkuImport(), $this->file_bku->getRealPath());
            $this->reset('file_bku');

            // Otomatis jalankan pipeline preprocessing jika data RKAS juga tersedia
            if (DataTrainingRkas::exists()) {
                (new PreprocessingPipeline())->process();
            }

            session()->flash('success', 'Data BKU baru berhasil diunggah!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengimpor data BKU: ' . $e->getMessage());
        }
    }

    public function downloadTemplate($type)
    {
        if ($type === 'rkas') {
            $headings = [
                'kode_rekening',
                'uraian_belanja',
                'volume_sebelum',
                'satuan_sebelum',
                'harga_satuan_sebelum',
                'jumlah_sebelum',
                'standar_komponen_s_sebelum',
                'standar_komponen_k_sebelum',
                'volume_setelah',
                'satuan_setelah',
                'harga_satuan_setelah',
                'jumlah_setelah',
                'standar_komponen_s_setelah',
                'standar_komponen_k_setelah',
                'bertambah_berkurang',
                'persentase_perubahan',
            ];

            return Excel::download(new TemplateExport($headings), 'template_data_latih_rkas.xlsx');
        } else {
            $headings = [
                'nomor_urut',
                'tanggal',
                'nomor_bukti',
                'uraian',
                'nilai_rincian',
                'kode_rekening',
                'penerimaan',
                'pengeluaran',
                'saldo',
                'label_aktual',
            ];

            return Excel::download(new TemplateExport($headings), 'template_data_latih_bku.xlsx');
        }
    }

    public function clearAllData()
    {
        DataTrainingRkas::truncate();
        DataTrainingBku::truncate();
        DataTraining::truncate();
        session()->flash('success', 'Seluruh data training berhasil dibersihkan!');
    }

    public function render()
    {
        $countWajar = DataTraining::where('label', 'Wajar')->count();
        $countTidakSesuai = DataTraining::whereIn('label', ['Tidak Sesuai', 'Menyimpang'])->count();
        $countTotal = DataTraining::count();

        return view('livewire.klasifikasi.import-data-training-manager', compact('countWajar', 'countTidakSesuai', 'countTotal'))
            ->layout('components.layouts.app', ['header' => 'Unggah Data Latih']);
    }
}
