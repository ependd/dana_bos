<?php

namespace App\Services;

use App\Models\Kebutuhan;
use App\Models\PeriodeBos;
use App\Models\Rkas;
use App\Models\RkasItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Service untuk pengelolaan RKAS.
 */
class RkasService
{
    /**
     * Generate RKAS dari konsolidasi kebutuhan yang sudah disetujui.
     */
    public function generateFromKebutuhan(PeriodeBos $periode): Rkas
    {
        $kebutuhans = Kebutuhan::where('periode_bos_id', $periode->id)
            ->whereIn('status', [Kebutuhan::STATUS_DITERIMA, 'disetujui', Kebutuhan::STATUS_DIAJUKAN])
            ->with('katalogBarang')
            ->get();

        if ($kebutuhans->isEmpty()) {
            throw new \RuntimeException("Belum ada data pengajuan kebutuhan dari Guru untuk Periode BOS {$periode->tahun_ajaran}. Dokumen RKAS tidak dapat dibuat.");
        }

        return DB::transaction(function () use ($periode, $kebutuhans) {
            // Hapus RKAS lama untuk periode ini jika sudah ada beserta file QR Code-nya
            $existingRkas = Rkas::where('periode_bos_id', $periode->id)->get();
            foreach ($existingRkas as $oldRkas) {
                if ($oldRkas->qr_code_path) {
                    Storage::disk('public')->delete($oldRkas->qr_code_path);
                }
                $oldRkas->delete();
            }

            // Hitung revisi ke-berapa (selalu 0 karena yang lama sudah dihapus)
            $revisiKe = 0;
            $kodeRkas = 'RKAS-' . str_replace('/', '', $periode->tahun_ajaran);

            $rkas = Rkas::create([
                'periode_bos_id' => $periode->id,
                'kode_rkas' => $kodeRkas,
                'status' => Rkas::STATUS_DRAFT,
                'total_anggaran' => 0,
                'revisi_ke' => $revisiKe,
            ]);

            // Konsolidasikan/group kebutuhan berdasarkan katalog_barang_id dan kategori_belanja
            $groupedKebutuhans = $kebutuhans->groupBy(function ($item) {
                return $item->katalog_barang_id . '_' . $item->kategori_belanja;
            });

            foreach ($groupedKebutuhans as $group) {
                $firstKebutuhan = $group->first();
                $totalVolume = $group->sum('volume');
                $harga = $firstKebutuhan->harga_satuan ?? $firstKebutuhan->katalogBarang->harga_referensi ?? 0;
                $jumlah = $totalVolume * $harga;

                // Buat item RKAS ter-konsolidasi
                $rkasItem = RkasItem::create([
                    'rkas_id' => $rkas->id,
                    'kebutuhan_id' => $firstKebutuhan->id, // Fallback ke kebutuhan pertama untuk referensi
                    'kode_rekening' => '',
                    'uraian_belanja' => $firstKebutuhan->katalogBarang->nama_barang ?? $firstKebutuhan->keterangan ?? '-',
                    'volume_setelah' => $totalVolume,
                    'satuan_setelah' => $firstKebutuhan->satuan,
                    'harga_satuan_setelah' => $harga,
                    'jumlah_setelah' => $jumlah,
                    'standar_komponen_s_setelah' => 0,
                    'standar_komponen_k_setelah' => 0,
                ]);

                // Update rkas_item_id di setiap kebutuhan di dalam group agar terhubung
                foreach ($group as $kebutuhan) {
                    $kebutuhan->update(['rkas_item_id' => $rkasItem->id]);
                }
            }

            $rkas->hitungTotalAnggaran();

            return $rkas;
        });
    }

    /**
     * Submit RKAS untuk persetujuan Kepala Sekolah.
     */
    public function submit(Rkas $rkas): Rkas
    {
        if (!$rkas->isEditable()) {
            throw new \RuntimeException('RKAS tidak dapat disubmit dalam status saat ini.');
        }

        $rkas->update([
            'status' => Rkas::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);

        return $rkas;
    }

    /**
     * Setujui RKAS (oleh Kepala Sekolah).
     */
    public function approve(Rkas $rkas, int $approvedBy): Rkas
    {
        if ($rkas->status !== Rkas::STATUS_SUBMITTED) {
            throw new \RuntimeException('RKAS belum disubmit.');
        }

        return DB::transaction(function () use ($rkas, $approvedBy) {
            $rkas->update([
                'status' => Rkas::STATUS_APPROVED,
                'approved_by' => $approvedBy,
                'approved_at' => now(),
            ]);

            // Generate QR Code
            $this->generateQrCode($rkas);

            return $rkas->fresh();
        });
    }

    /**
     * Tolak RKAS (oleh Kepala Sekolah).
     */
    public function reject(Rkas $rkas, string $catatan): Rkas
    {
        if ($rkas->status !== Rkas::STATUS_SUBMITTED) {
            throw new \RuntimeException('RKAS belum disubmit.');
        }

        $rkas->update([
            'status' => Rkas::STATUS_REJECTED,
            'catatan_penolakan' => $catatan,
        ]);

        return $rkas;
    }

    /**
     * Ajukan revisi RKAS.
     */
    public function revise(Rkas $rkas): Rkas
    {
        return DB::transaction(function () use ($rkas) {
            $rkas->update([
                'status' => Rkas::STATUS_REVISED,
                'revisi_ke' => $rkas->revisi_ke + 1,
            ]);

            // Copy setelah to sebelum for all items
            foreach ($rkas->items as $item) {
                $item->update([
                    'volume_sebelum' => $item->volume_setelah,
                    'satuan_sebelum' => $item->satuan_setelah,
                    'harga_satuan_sebelum' => $item->harga_satuan_setelah,
                    'jumlah_sebelum' => $item->jumlah_setelah,
                    'standar_komponen_s_sebelum' => $item->standar_komponen_s_setelah,
                    'standar_komponen_k_sebelum' => $item->standar_komponen_k_setelah,
                ]);
            }

            return $rkas;
        });
    }

    public function generateQrCode(Rkas $rkas): string
    {
        // Public verification URL encoded into the QR Code
        $verifyUrl = route('rkas.verify', ['kode' => $rkas->kode_rkas]);

        $directory = 'qrcodes';
        $filename = "qr_rkas_{$rkas->id}.svg";
        $path = "{$directory}/{$filename}";

        // Pastikan direktori ada
        Storage::disk('public')->makeDirectory($directory);

        // Generate QR Code as SVG with verification URL
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->generate($verifyUrl);

        Storage::disk('public')->put($path, $qrCode);

        $rkas->update(['qr_code_path' => $path]);

        return $path;
    }
}
