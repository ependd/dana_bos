<?php

namespace App\Livewire\Bku;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Bku;
use App\Models\BkuItem;
use App\Models\Rkas;
use App\Models\RkasItem;
use App\Models\PeriodeBos;

class CreateBkuItem extends Component
{
    use WithFileUploads;

    public ?int $rkas_item_id = null;
    public string $tanggal = '';
    public ?string $nomor_bukti = null;
    public string $uraian = '';
    public $pengeluaran = 0;
    public $foto_barang = null;
    public ?int $selectedBkuId = null;

    public function mount()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Akses Ditolak. Hanya Admin/Bendahara yang dapat mengakses halaman ini.');
        }

        $this->tanggal = date('Y-m-d');

        $activePeriode = PeriodeBos::where('is_active', true)->first();
        if ($activePeriode) {
            $bku = Bku::where('periode_bos_id', $activePeriode->id)->first();

            if (!$bku) {
                $approvedRkas = Rkas::where('periode_bos_id', $activePeriode->id)
                    ->where('status', 'approved')
                    ->first();

                if ($approvedRkas) {
                    $bku = Bku::create([
                        'rkas_id' => $approvedRkas->id,
                        'periode_bos_id' => $activePeriode->id,
                        'kode_bku' => 'BKU-' . str_replace('/', '', $activePeriode->tahun_ajaran),
                        'status' => Bku::STATUS_AKTIF,
                        'total_realisasi' => 0,
                    ]);
                }
            }

            if ($bku) {
                $this->selectedBkuId = $bku->id;
            }
        }
    }

    public function updatedRkasItemId($value)
    {
        if ($value) {
            $rkasItem = RkasItem::find($value);
            if ($rkasItem) {
                $this->uraian = $rkasItem->uraian_belanja;
                $val = (float) $rkasItem->jumlah_setelah;
                $this->pengeluaran = floor($val) == $val ? (int)$val : $val;
            }
        } else {
            $this->uraian = '';
            $this->pengeluaran = 0;
        }
    }

    public function save()
    {
        if (!auth()->user()->isAdmin()) {
            session()->flash('error', 'Hanya Admin/Bendahara yang dapat menginput transaksi BKU.');
            return;
        }

        $this->validate([
            'rkas_item_id' => 'required|exists:rkas_items,id',
            'tanggal' => 'required|date',
            'nomor_bukti' => 'required|string|max:50',
            'uraian' => 'required|string|max:255',
            'pengeluaran' => 'required|numeric|min:0',
            'foto_barang' => 'required|image|max:2048',
        ], [
            'rkas_item_id.required' => 'Wajib memilih item barang yang telah disetujui Kepsek.',
            'nomor_bukti.required' => 'Nomor bukti nota/kwitansi wajib diisi.',
            'foto_barang.required' => 'Wajib mengunggah foto bukti fisik barang saat pengisian BKU Realisasi.',
            'foto_barang.image' => 'File foto bukti barang harus berupa gambar (JPG/PNG).',
        ]);

        $rkasItem = RkasItem::findOrFail($this->rkas_item_id);
        $photoPath = $this->foto_barang->store('bku_photos', 'public');

        if (!$this->selectedBkuId) {
            $activePeriode = PeriodeBos::where('is_active', true)->first();
            if ($activePeriode) {
                $bku = Bku::where('periode_bos_id', $activePeriode->id)->first();
                if (!$bku) {
                    $approvedRkas = Rkas::where('periode_bos_id', $activePeriode->id)->where('status', 'approved')->first();
                    if ($approvedRkas) {
                        $bku = Bku::create([
                            'rkas_id' => $approvedRkas->id,
                            'periode_bos_id' => $activePeriode->id,
                            'kode_bku' => 'BKU-' . str_replace('/', '', $activePeriode->tahun_ajaran),
                            'status' => Bku::STATUS_AKTIF,
                            'total_realisasi' => 0,
                        ]);
                    }
                }
                $this->selectedBkuId = $bku?->id;
            }
        }

        $bku = Bku::findOrFail($this->selectedBkuId);
        $nomorUrut = $bku->items()->count() + 1;

        $lastItem = $bku->items()->orderBy('nomor_urut', 'desc')->first();
        $lastSaldo = $lastItem ? $lastItem->saldo : $bku->total_penerimaan;

        BkuItem::create([
            'bku_id' => $bku->id,
            'rkas_item_id' => $rkasItem->id,
            'nomor_urut' => $nomorUrut,
            'tanggal' => $this->tanggal,
            'nomor_bukti' => $this->nomor_bukti ?? ('BKU/BK-' . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT)),
            'uraian' => $this->uraian,
            'kode_rekening' => $rkasItem->kode_rekening,
            'penerimaan' => 0,
            'pengeluaran' => $this->pengeluaran,
            'saldo' => $lastSaldo - $this->pengeluaran,
            'foto_barang' => $photoPath,
        ]);

        $bku->hitungTotalRealisasi();
        session()->flash('success', 'Transaksi realisasi BKU beserta foto bukti barang berhasil disimpan!');

        return redirect()->route('bku.index');
    }

    public function render()
    {
        $availableRkasItems = RkasItem::whereHas('rkas', fn ($q) => $q->where('status', 'approved'))
            ->where('status_barang_datang', 'diterima')
            ->whereDoesntHave('bkuItems')
            ->get();

        return view('livewire.bku.create-bku-item', compact('availableRkasItems'))
            ->layout('components.layouts.app', ['header' => 'Catat Realisasi BKU']);
    }
}
