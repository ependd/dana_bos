<?php

namespace App\Http\Controllers;

use App\Models\HasilKlasifikasi;
use App\Models\PeriodeBos;
use App\Models\Rkas;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MonitoringPdfController extends Controller
{
    /**
     * Download PDF Laporan Monitoring Realisasi Anggaran BOS.
     * Hanya pengguna dengan role kepala_sekolah yang diizinkan mengunduh.
     */
    public function download(Request $request, $periodeId)
    {
        if (!$request->user()?->isKepalaSekolah()) {
            abort(403, 'Akses ditolak. Hanya Kepala Sekolah yang diizinkan mengunduh Laporan PDF Monitoring.');
        }

        $periode = PeriodeBos::findOrFail($periodeId);

        // Ambil data hasil klasifikasi untuk periode ini (prioritas WOA)
        $hasil = HasilKlasifikasi::where('periode_bos_id', $periodeId)
            ->where('metode', 'woa')
            ->with(['bkuItem', 'rkasItem', 'classifier'])
            ->orderBy('kode_rekening')
            ->get();

        if ($hasil->isEmpty()) {
            $hasil = HasilKlasifikasi::where('periode_bos_id', $periodeId)
                ->with(['bkuItem', 'rkasItem', 'classifier'])
                ->orderBy('kode_rekening')
                ->get();
        }

        $rkas = Rkas::where('periode_bos_id', $periodeId)
            ->where('status', Rkas::STATUS_APPROVED)
            ->with('approver')
            ->orderByDesc('revisi_ke')
            ->first();

        // Hitung Statistik
        $totalTransaksi = $hasil->count();
        $totalWajar = $hasil->where('label_prediksi', 'Wajar')->count();
        $totalMenyimpang = $hasil->filter(fn ($item) => $item->isMenyimpang())->count();
        $persentaseWajar = $totalTransaksi > 0 ? round(($totalWajar / $totalTransaksi) * 100, 2) : 0;

        // Tanggal Bahasa Indonesia
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

        // Format Kertas A4 Portrait
        $pdf = Pdf::loadView('pdf.laporan-monitoring', $data)
            ->setPaper('a4', 'portrait');

        $safeTahun = str_replace(['/', '\\'], '-', $periode->tahun_ajaran);

        return $pdf->download("Laporan_Monitoring_BOS_{$safeTahun}.pdf");
    }
}
