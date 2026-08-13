<?php

namespace App\Livewire\Rkas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RkasItem;

class KonfirmasiBarangDatangManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = 'semua'; // semua, belum_datang, diterima

    // Bulk action selection state
    public array $selectedIds = [];
    public bool $selectAll = false;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $query = RkasItem::whereHas('rkas', fn ($q) => $q->where('status', 'approved'))
                ->where('status_barang_datang', '!=', 'diterima')
                ->when($this->search, fn ($q) => $q->where('uraian_belanja', 'like', "%{$this->search}%"))
                ->when($this->filterStatus !== 'semua', fn ($q) => $q->where('status_barang_datang', $this->filterStatus));

            $this->selectedIds = $query->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function updatedSelectedIds()
    {
        $this->selectAll = false;
    }

    public function bulkConfirmArrival()
    {
        if (!auth()->user()->isKepalaSekolah() && !auth()->user()->isAdmin()) {
            session()->flash('error', 'Hanya Kepala Sekolah / Admin yang memiliki wewenang mengonfirmasi penerimaan barang.');
            return;
        }

        if (empty($this->selectedIds)) {
            session()->flash('error', 'Pilih minimal satu item barang untuk dikonfirmasi.');
            return;
        }

        $count = RkasItem::whereIn('id', $this->selectedIds)
            ->where('status_barang_datang', '!=', 'diterima')
            ->update([
                'status_barang_datang' => 'diterima',
                'tanggal_barang_datang' => now(),
            ]);

        $this->selectedIds = [];
        $this->selectAll = false;

        session()->flash('success', "Berhasil mengonfirmasi penerimaan {$count} item barang sekaligus!");
    }

    public function confirmArrival(int $id)
    {
        if (!auth()->user()->isKepalaSekolah() && !auth()->user()->isAdmin()) {
            session()->flash('error', 'Hanya Kepala Sekolah / Admin yang memiliki wewenang mengonfirmasi penerimaan barang.');
            return;
        }

        $item = RkasItem::findOrFail($id);
        $item->update([
            'status_barang_datang' => 'diterima',
            'tanggal_barang_datang' => now(),
        ]);

        session()->flash('success', "Barang '{$item->uraian_belanja}' telah dikonfirmasi diterima fisik di sekolah!");
    }

    public function render()
    {
        $items = RkasItem::with(['rkas.periodeBos', 'kebutuhan.user'])
            ->whereHas('rkas', fn ($q) => $q->where('status', 'approved'))
            ->when($this->search, fn ($q) => $q->where('uraian_belanja', 'like', "%{$this->search}%"))
            ->when($this->filterStatus !== 'semua', fn ($q) => $q->where('status_barang_datang', $this->filterStatus))
            ->orderBy('id', 'desc')
            ->paginate(12);

        return view('livewire.rkas.konfirmasi-barang-datang-manager', compact('items'))
            ->layout('components.layouts.app', ['header' => 'Konfirmasi Barang Datang']);
    }
}
