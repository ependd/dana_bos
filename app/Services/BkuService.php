<?php

namespace App\Services;

use App\Models\Bku;
use App\Models\BkuItem;
use App\Models\Kebutuhan;
use App\Models\Rkas;
use App\Models\RkasItem;
use App\Models\User;
use App\Notifications\BarangTersediaNotification;
use Illuminate\Support\Facades\DB;

/**
 * Service untuk pengelolaan BKU dan realisasi anggaran.
 */
class BkuService
{
    /**
     * Generate BKU dari RKAS yang sudah disahkan.
     */
    public function generateFromRkas(Rkas $rkas): Bku
    {
        if ($rkas->status !== Rkas::STATUS_APPROVED) {
            throw new \RuntimeException('RKAS belum disetujui. BKU hanya dapat di-generate dari RKAS yang sudah disetujui.');
        }

        // Cek apakah BKU sudah ada
        if ($rkas->bku) {
            return $rkas->bku;
        }

        return DB::transaction(function () use ($rkas) {
            $bku = Bku::create([
                'rkas_id' => $rkas->id,
                'periode_bos_id' => $rkas->periode_bos_id,
                'kode_bku' => 'BKU-' . $rkas->kode_rkas,
                'status' => Bku::STATUS_AKTIF,
                'total_realisasi' => 0,
            ]);

            // Generate BKU items dari RKAS items (template kosong)
            $nomor = 1;
            foreach ($rkas->items as $rkasItem) {
                BkuItem::create([
                    'bku_id' => $bku->id,
                    'rkas_item_id' => $rkasItem->id,
                    'nomor_urut' => $nomor++,
                    'tanggal' => now(),
                    'uraian' => $rkasItem->uraian_belanja,
                    'kode_rekening' => $rkasItem->kode_rekening,
                    'penerimaan' => 0,
                    'pengeluaran' => 0,
                    'saldo' => 0,
                ]);
            }

            return $bku;
        });
    }

    /**
     * Input realisasi untuk BKU item.
     */
    public function inputRealisasi(BkuItem $bkuItem, array $data): BkuItem
    {
        $bkuItem->update([
            'tanggal' => $data['tanggal'] ?? $bkuItem->tanggal,
            'nomor_bukti' => $data['nomor_bukti'] ?? $bkuItem->nomor_bukti,
            'uraian' => $data['uraian'] ?? $bkuItem->uraian,
            'nilai_rincian' => $data['nilai_rincian'] ?? $bkuItem->nilai_rincian,
            'penerimaan' => $data['penerimaan'] ?? $bkuItem->penerimaan,
            'pengeluaran' => $data['pengeluaran'] ?? $bkuItem->pengeluaran,
            'saldo' => $data['saldo'] ?? $bkuItem->saldo,
        ]);

        // Update total realisasi BKU
        $bkuItem->bku->hitungTotalRealisasi();

        // Notifikasi guru jika ada realisasi pengeluaran (barang tersedia)
        if (($data['pengeluaran'] ?? 0) > 0) {
            $this->notifyGuru($bkuItem);
        }

        return $bkuItem;
    }

    /**
     * Kirim notifikasi ke guru terkait bahwa barang sudah tersedia.
     */
    public function notifyGuru(BkuItem $bkuItem): void
    {
        $kebutuhans = collect();

        if ($bkuItem->rkasItem) {
            // 1. Ambil semua kebutuhan yang terhubung lewat rkas_item_id (hasil konsolidasi baru)
            $linkedKebutuhans = Kebutuhan::where('rkas_item_id', $bkuItem->rkas_item_id)->get();
            if ($linkedKebutuhans->isNotEmpty()) {
                $kebutuhans = $kebutuhans->merge($linkedKebutuhans);
            }

            // 2. Fallback untuk kebutuhan tunggal model lama (lewat kebutuhan_id)
            if ($bkuItem->rkasItem->kebutuhan) {
                $kebutuhans->push($bkuItem->rkasItem->kebutuhan);
            }
        }

        // Hilangkan duplikat kebutuhan
        $kebutuhans = $kebutuhans->unique('id');

        foreach ($kebutuhans as $kebutuhan) {
            // Update status kebutuhan menjadi tersedia
            $kebutuhan->update(['status' => Kebutuhan::STATUS_TERSEDIA]);

            // Kirim notifikasi ke guru yang mengajukan
            $guru = $kebutuhan->user;
            if ($guru) {
                $guru->notify(new BarangTersediaNotification($kebutuhan, $bkuItem));
            }
        }
    }

    /**
     * Hitung ulang saldo BKU secara berurutan.
     */
    public function recalculateSaldo(Bku $bku): void
    {
        $saldo = 0;
        $items = $bku->items()->orderBy('nomor_urut')->get();

        foreach ($items as $item) {
            $saldo = $saldo + $item->penerimaan - $item->pengeluaran;
            $item->update(['saldo' => $saldo]);
        }
    }

    /**
     * Trigger status "Tersedia" dan kirim notifikasi ke Guru untuk semua pengeluaran di BKU ini.
     */
    public function triggerNotificationsForBku(Bku $bku): void
    {
        $items = $bku->items()->where('pengeluaran', '>', 0)->get();
        foreach ($items as $item) {
            $this->notifyGuru($item);
        }
    }
}
