<?php

namespace App\Livewire\Bku;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Bku;
use App\Models\BkuItem;
use App\Models\Rkas;
use App\Models\RkasItem;
use App\Models\PeriodeBos;
use Illuminate\Support\Facades\Storage;

class BkuManager extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public ?int $selectedBkuId = null;

    // Bulk selection fields
    public array $selectedBkuItems = [];
    public bool $selectAll = false;

    // Item Form Modal
    public bool $showItemModal = false;
    public bool $showPhotoModal = false;
    public ?string $previewPhotoUrl = null;

    // Form fields
    public ?int $rkas_item_id = null;
    public string $tanggal = '';
    public ?string $nomor_bukti = null;
    public string $uraian = '';
    public $pengeluaran = 0;
    public $foto_barang = null;

    // Photo upload modal fields for existing items
    public ?int $editingPhotoItemId = null;
    public $new_foto_barang = null;

    public function mount()
    {
        $this->tanggal = date('Y-m-d');

        // Auto create/fetch active BKU header
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

    public function updatedSelectAll($value)
    {
        if ($value && $this->selectedBkuId) {
            $this->selectedBkuItems = BkuItem::where('bku_id', $this->selectedBkuId)
                ->when($this->search, fn ($q) => $q->where('uraian', 'like', "%{$this->search}%"))
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedBkuItems = [];
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->selectedBkuItems = [];
        $this->selectAll = false;
    }

    public function openItemModal()
    {
        $this->reset(['rkas_item_id', 'nomor_bukti', 'uraian', 'pengeluaran', 'foto_barang']);
        $this->tanggal = date('Y-m-d');
        $this->dispatch('open-modal', 'bku-item-modal');
    }

    public function updatedRkasItemId($value)
    {
        if ($value) {
            $rkasItem = RkasItem::find($value);
            if ($rkasItem) {
                $this->uraian = $rkasItem->nama_barang;
                $this->pengeluaran = $rkasItem->total_harga;
            }
        }
    }

    public function saveItem()
    {
        if (!auth()->user()->isAdmin()) {
            session()->flash('error', 'Hanya Admin/Bendahara yang dapat menambahkan pengeluaran BKU.');
            return;
        }

        $this->validate([
            'rkas_item_id' => 'required|exists:rkas_items,id',
            'tanggal' => 'required|date',
            'nomor_bukti' => 'nullable|string|max:50',
            'uraian' => 'required|string|max:255',
            'pengeluaran' => 'required|numeric|min:1',
            'foto_barang' => 'required|image|max:2048',
        ], [
            'rkas_item_id.required' => 'Wajib memilih item barang dari RKAS yang disetujui.',
            'foto_barang.required' => 'Wajib mengunggah foto bukti fisik barang.',
            'foto_barang.image' => 'File foto bukti barang harus berupa gambar (JPG/PNG).',
        ]);

        $rkasItem = RkasItem::findOrFail($this->rkas_item_id);

        $photoPath = $this->foto_barang->store('bku_photos', 'public');

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
        $this->dispatch('close-modal', 'bku-item-modal');
        session()->flash('success', 'Transaksi BKU realisasi beserta foto bukti barang berhasil disimpan!');
    }

    public function deleteItem(int $id)
    {
        if (!auth()->user()->isAdmin()) return;

        $item = BkuItem::findOrFail($id);
        $bku = $item->bku;

        if ($item->foto_barang && Storage::disk('public')->exists($item->foto_barang)) {
            Storage::disk('public')->delete($item->foto_barang);
        }

        $item->delete();

        if ($bku) {
            $bku->hitungTotalRealisasi();
        }

        $this->selectedBkuItems = array_diff($this->selectedBkuItems, [(string)$id]);
        session()->flash('success', 'Transaksi BKU berhasil dihapus!');
    }

    public function deleteSelected()
    {
        if (!auth()->user()->isAdmin() || empty($this->selectedBkuItems)) return;

        $items = BkuItem::whereIn('id', $this->selectedBkuItems)->get();
        $count = 0;

        foreach ($items as $item) {
            if ($item->foto_barang && Storage::disk('public')->exists($item->foto_barang)) {
                Storage::disk('public')->delete($item->foto_barang);
            }
            $item->delete();
            $count++;
        }

        if ($this->selectedBkuId) {
            $bku = Bku::find($this->selectedBkuId);
            if ($bku) {
                $bku->hitungTotalRealisasi();
            }
        }

        $this->selectedBkuItems = [];
        $this->selectAll = false;

        session()->flash('success', "{$count} transaksi BKU berhasil dihapus!");
    }

    public function openUploadPhotoModal(int $itemId)
    {
        $this->editingPhotoItemId = $itemId;
        $this->reset('new_foto_barang');
        $this->dispatch('open-modal', 'upload-photo-modal');
    }

    public function saveItemPhoto()
    {
        if (!auth()->user()->isAdmin()) return;

        $this->validate([
            'new_foto_barang' => 'required|image|max:2048',
        ], [
            'new_foto_barang.required' => 'Wajib memilih foto bukti fisik barang.',
            'new_foto_barang.image' => 'File foto bukti barang harus berupa gambar (JPG/PNG).',
        ]);

        if ($this->editingPhotoItemId) {
            $item = BkuItem::findOrFail($this->editingPhotoItemId);

            if ($item->foto_barang && Storage::disk('public')->exists($item->foto_barang)) {
                Storage::disk('public')->delete($item->foto_barang);
            }

            $photoPath = $this->new_foto_barang->store('bku_photos', 'public');
            $item->update(['foto_barang' => $photoPath]);

            $this->dispatch('close-modal', 'upload-photo-modal');
            session()->flash('success', 'Foto bukti fisik barang berhasil diunggah!');
        }
    }

    public function render()
    {
        $bku = $this->selectedBkuId ? Bku::with('periodeBos')->find($this->selectedBkuId) : null;

        $items = $bku ? BkuItem::with('rkasItem')
            ->where('bku_id', $bku->id)
            ->when($this->search, fn ($q) => $q->where('uraian', 'like', "%{$this->search}%"))
            ->orderBy('nomor_urut', 'asc')
            ->paginate(15) : collect();

        // Rule 4 Filter: Only show RKAS items that have status_barang_datang == 'diterima'
        $availableRkasItems = RkasItem::whereHas('rkas', fn ($q) => $q->where('status', 'approved'))
            ->where('status_barang_datang', 'diterima')
            ->whereDoesntHave('bkuItems')
            ->get();

        return view('livewire.bku.bku-manager', compact('bku', 'items', 'availableRkasItems'))
            ->layout('components.layouts.app', ['header' => 'Buku Kas Umum']);
    }
}
