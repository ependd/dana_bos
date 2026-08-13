<?php

namespace App\Services;

use App\Models\HasilKlasifikasi;
use App\Models\PeriodeBos;
use App\Models\Rkas;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

/**
 * Service untuk generate laporan monitoring dalam format PDF.
 */
class PdfReportService
{
    /**
     * Generate laporan monitoring PDF untuk periode tertentu.
     */
    public function generateMonitoringReport(int $periodeBosId): string
    {
        $periode = PeriodeBos::findOrFail($periodeBosId);
        $hasil = HasilKlasifikasi::where('periode_bos_id', $periodeBosId)
            ->where('metode', 'woa')
            ->with(['bkuItem', 'rkasItem', 'classifier'])
            ->orderBy('kode_rekening')
            ->get();

        if ($hasil->isEmpty()) {
            $hasil = HasilKlasifikasi::where('periode_bos_id', $periodeBosId)
                ->with(['bkuItem', 'rkasItem', 'classifier'])
                ->orderBy('kode_rekening')
                ->get();
        }

        $rkas = Rkas::where('periode_bos_id', $periodeBosId)
            ->where('status', Rkas::STATUS_APPROVED)
            ->with('approver')
            ->orderByDesc('revisi_ke')
            ->first();

        // Statistik
        $totalTransaksi = $hasil->count();
        $totalWajar = $hasil->where('label_prediksi', 'Wajar')->count();
        $totalMenyimpang = $hasil->filter(fn ($item) => $item->isMenyimpang())->count();
        $persentaseWajar = $totalTransaksi > 0 ? round(($totalWajar / $totalTransaksi) * 100, 2) : 0;

        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $now = now();
        $tanggalCetak = $now->day . ' ' . $bulanIndo[(int)$now->month] . ' ' . $now->year;

        $data = [
            'periode' => $periode,
            'hasil' => $hasil,
            'rkas' => $rkas,
            'totalTransaksi' => $totalTransaksi,
            'totalWajar' => $totalWajar,
            'totalMenyimpang' => $totalMenyimpang,
            'persentaseWajar' => $persentaseWajar,
            'tanggalCetak' => $tanggalCetak,
        ];

        $pdf = Pdf::loadView('pdf.laporan-monitoring', $data)
            ->setPaper('a4', 'portrait');

        $directory = 'laporan';
        $safeTahunAjaran = str_replace('/', '-', $periode->tahun_ajaran);
        $filename = "monitoring_{$safeTahunAjaran}_{$periode->id}.pdf";
        $path = "{$directory}/{$filename}";

        Storage::disk('public')->makeDirectory($directory);
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
}
