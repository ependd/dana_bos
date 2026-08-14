<?php

namespace App\Livewire\Kebutuhan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Kebutuhan;
use App\Models\PeriodeBos;
use App\Models\KatalogBarang;
use App\Models\Kategori;

class KebutuhanManager extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $filterStatus = null;

    // Modal state
    public bool $showModal = false;
    public bool $showRejectModal = false;
    public bool $isEdit = false;
    public ?int $kebutuhanId = null;

    // Bulk action state for Admin
    public array $selectedIds = [];
    public bool $selectAll = false;
    public ?int $deleteTargetId = null;
    public bool $isBulkDelete = false;
    public bool $isBulkReject = false;

    // Form fields (for adding individual item to cart)
    public ?int $periode_bos_id = null;
    public ?string $kategori_belanja = null;
    public ?int $katalog_barang_id = null;
    public int $volume = 1;
    public string $satuan = 'Pcs';

    // Page view mode: 'list' or 'create'
    public string $viewMode = 'list';

    // Multi-item cart for batch submission (Guru)
    public array $items = [];
    public array $selectedCartIndexes = [];
    public bool $selectAllCart = false;

    // Admin Rejection field (Dropdown formal)
    public string $catatan_admin_dropdown = '';
    public array $rejectionOptions = [
        "Permintaan tidak sesuai dengan posisi/pos anggaran belanja sekolah",
        "Harga estimasi barang melampaui standar harga katalog yang berlaku",
        "Stok/ketersediaan barang masih mencukupi di sekolah",
        "Kuota/pagu anggaran pada periode ini telah melampaui batas",
    ];

    public function mount()
    {
        $activePeriode = PeriodeBos::where('is_active', true)->first() 
                      ?? PeriodeBos::where('status_pengajuan', true)->first();
        $this->periode_bos_id = $activePeriode?->id;
        $this->catatan_admin_dropdown = $this->rejectionOptions[0];
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $user = auth()->user();
            $query = Kebutuhan::query()
                ->when($user->isGuru(), fn ($q) => $q->where('user_id', $user->id))
                ->when($this->search, function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('nama_barang', 'like', "%{$this->search}%")
                            ->orWhereHas('katalogBarang', fn ($kb) => $kb->where('nama_barang', 'like', "%{$this->search}%"))
                            ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%"));
                    });
                })
                ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus));

            $this->selectedIds = $query->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function updatedSelectedIds()
    {
        $this->selectAll = false;
    }

    public function updatedKategoriBelanja()
    {
        $this->katalog_barang_id = null;
        $this->satuan = 'Pcs';
    }

    public function updatedKatalogBarangId($value)
    {
        if ($value) {
            $barang = KatalogBarang::find($value);
            if ($barang) {
                $this->satuan = $barang->satuan;
            }
        }
    }

    public function openCreatePage()
    {
        $user = auth()->user();
        
        if (!$user->isGuru()) {
            session()->flash('error', 'Hanya Guru yang dapat membuat pengajuan kebutuhan.');
            return;
        }

        // Rule 1: Limit 1 submission per Guru per Active Period
        $existing = Kebutuhan::where('user_id', $user->id)
            ->where('periode_bos_id', $this->periode_bos_id)
            ->exists();
        if ($existing) {
            session()->flash('error', 'Anda sudah mengajukan kebutuhan pada periode ini. Setiap Guru hanya dapat mengajukan 1 permintaan per periode.');
            return;
        }

        $this->resetItemForm();
        $this->items = [];
        $this->selectedCartIndexes = [];
        $this->selectAllCart = false;
        $this->isEdit = false;
        $this->viewMode = 'create';
    }

    public function backToList()
    {
        $this->viewMode = 'list';
        $this->resetItemForm();
        $this->items = [];
        $this->selectedCartIndexes = [];
        $this->selectAllCart = false;
    }

    public function openEditModal(int $id)
    {
        $kebutuhan = Kebutuhan::findOrFail($id);

        if ($kebutuhan->status !== 'diajukan') {
            session()->flash('error', 'Hanya pengajuan dengan status "diajukan" yang dapat diedit.');
            return;
        }

        $this->kebutuhanId = $kebutuhan->id;
        $this->isEdit = true;

        $this->kategori_belanja = $kebutuhan->kategori_belanja;
        $this->katalog_barang_id = $kebutuhan->katalog_barang_id;
        $this->volume = $kebutuhan->volume;
        $this->satuan = $kebutuhan->satuan;

        $this->resetValidation();
        $this->dispatch('open-modal', 'edit-kebutuhan-modal');
    }

    public function update()
    {
        $this->validate([
            'volume' => 'required|integer|min:1',
        ]);

        if (!$this->kebutuhanId) return;

        $kebutuhan = Kebutuhan::findOrFail($this->kebutuhanId);

        if ($kebutuhan->status !== 'diajukan') {
            session()->flash('error', 'Pengajuan yang telah diproses tidak dapat diedit.');
            $this->dispatch('close-modal', 'edit-kebutuhan-modal');
            return;
        }

        $kebutuhan->update([
            'volume' => $this->volume,
            'status' => 'diterima',
            'catatan_admin' => null,
            'tanggal_konfirmasi' => now(),
        ]);

        $this->resetItemForm();
        $this->isEdit = false;
        $this->kebutuhanId = null;

        $this->dispatch('close-modal', 'edit-kebutuhan-modal');
        session()->flash('success', 'Kuantitas kebutuhan berhasil diperbarui dan disetujui!');
    }

    public function openRejectModal(int $id)
    {
        $this->kebutuhanId = $id;
        $this->isBulkReject = false;
        $this->catatan_admin_dropdown = $this->rejectionOptions[0];
        $this->js("window.dispatchEvent(new CustomEvent('open-modal', { detail: 'reject-modal' }))");
    }

    public function openBulkRejectModal()
    {
        if (empty($this->selectedIds)) return;
        $this->isBulkReject = true;
        $this->catatan_admin_dropdown = $this->rejectionOptions[0];
        $this->js("window.dispatchEvent(new CustomEvent('open-modal', { detail: 'reject-modal' }))");
    }

    public function openDeleteModal(?int $id = null)
    {
        if ($id) {
            $this->deleteTargetId = $id;
            $this->isBulkDelete = false;
        } else {
            if (empty($this->selectedIds)) return;
            $this->deleteTargetId = null;
            $this->isBulkDelete = true;
        }
        $this->js("window.dispatchEvent(new CustomEvent('open-modal', { detail: 'delete-confirm-modal' }))");
    }

    /**
     * Reset only the item input form, not the items cart
     */
    private function resetItemForm()
    {
        $this->kategori_belanja = null;
        $this->katalog_barang_id = null;
        $this->volume = 1;
        $this->satuan = 'Pcs';
    }

    /**
     * Add current form item to the items cart
     */
    public function addItem()
    {
        $this->validate([
            'kategori_belanja' => 'required|string',
            'katalog_barang_id' => 'required|exists:katalog_barangs,id',
            'volume' => 'required|integer|min:1',
            'satuan' => 'required|string',
        ]);

        $barang = KatalogBarang::find($this->katalog_barang_id);

        // Prevent duplicate barang in the same cart
        foreach ($this->items as $item) {
            if ($item['katalog_barang_id'] == $this->katalog_barang_id) {
                $this->addError('katalog_barang_id', 'Barang ini sudah ada di daftar pengajuan.');
                return;
            }
        }

        $this->items[] = [
            'kategori_belanja' => $this->kategori_belanja,
            'katalog_barang_id' => $this->katalog_barang_id,
            'nama_barang' => $barang->nama_barang,
            'harga_referensi' => $barang->harga_referensi,
            'harga_satuan' => $barang->harga_referensi,
            'volume' => $this->volume,
            'satuan' => $this->satuan,
        ];

        $this->resetItemForm();
    }

    public function updatedSelectAllCart($value)
    {
        if ($value) {
            $this->selectedCartIndexes = array_map('strval', array_keys($this->items));
        } else {
            $this->selectedCartIndexes = [];
        }
    }

    public function updatedSelectedCartIndexes()
    {
        $this->selectAllCart = false;
    }

    public function removeSelectedCartItems()
    {
        if (empty($this->selectedCartIndexes)) return;

        foreach ($this->selectedCartIndexes as $index) {
            unset($this->items[(int)$index]);
        }
        $this->items = array_values($this->items);
        $this->selectedCartIndexes = [];
        $this->selectAllCart = false;
    }

    /**
     * Remove item from cart by index
     */
    public function removeItem(int $index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->selectedCartIndexes = [];
        $this->selectAllCart = false;
    }

    /**
     * Submit all items in the cart as kebutuhan records (batch)
     */
    public function save()
    {
        $user = auth()->user();

        // Check active period
        $periode = PeriodeBos::find($this->periode_bos_id);
        if (!$periode || !$periode->status_pengajuan) {
            session()->flash('error', 'Saat ini periode pengajuan kebutuhan sedang ditutup.');
            return;
        }

        // Rule 1: Double check for Guru limit
        if ($user->isGuru() && !$this->isEdit) {
            $existing = Kebutuhan::where('user_id', $user->id)
                ->where('periode_bos_id', $this->periode_bos_id)
                ->exists();
            if ($existing) {
                session()->flash('error', 'Anda telah mencapai batas 1 pengajuan per periode.');
                return;
            }
        }

        if (empty($this->items)) {
            session()->flash('error', 'Tambahkan minimal 1 item barang sebelum mengirim pengajuan.');
            return;
        }

        // Create all items in batch
        foreach ($this->items as $item) {
            Kebutuhan::create([
                'user_id' => $user->id,
                'periode_bos_id' => $this->periode_bos_id,
                'kategori_belanja' => $item['kategori_belanja'],
                'katalog_barang_id' => $item['katalog_barang_id'],
                'nama_barang' => $item['nama_barang'],
                'harga_satuan' => $item['harga_satuan'] ?? $item['harga_referensi'] ?? 0,
                'volume' => $item['volume'],
                'satuan' => $item['satuan'],
                'status' => 'diajukan',
            ]);
        }

        $count = count($this->items);
        $this->items = [];
        $this->selectedCartIndexes = [];
        $this->selectAllCart = false;
        $this->viewMode = 'list';
        session()->flash('success', "Berhasil mengajukan {$count} item kebutuhan!");
    }

    public function approve(int $id)
    {
        $kebutuhan = Kebutuhan::findOrFail($id);
        $kebutuhan->update([
            'status' => 'diterima',
            'catatan_admin' => null,
            'tanggal_konfirmasi' => now(),
        ]);

        session()->flash('success', 'Pengajuan kebutuhan telah disetujui!');
    }

    public function bulkApprove()
    {
        if (empty($this->selectedIds)) return;

        $count = Kebutuhan::whereIn('id', $this->selectedIds)->update([
            'status' => 'diterima',
            'catatan_admin' => null,
            'tanggal_konfirmasi' => now(),
        ]);

        $this->selectedIds = [];
        $this->selectAll = false;

        session()->flash('success', "Berhasil menyetujui {$count} pengajuan sekaligus!");
    }

    public function rejectConfirm()
    {
        if ($this->isBulkReject) {
            if (empty($this->selectedIds)) return;

            $count = Kebutuhan::whereIn('id', $this->selectedIds)->update([
                'status' => 'ditolak',
                'catatan_admin' => $this->catatan_admin_dropdown,
                'tanggal_konfirmasi' => now(),
            ]);

            $this->selectedIds = [];
            $this->selectAll = false;
            $this->isBulkReject = false;

            $this->js("window.dispatchEvent(new CustomEvent('close-modal', { detail: 'reject-modal' }))");
            session()->flash('success', "Berhasil menolak {$count} pengajuan sekaligus!");
            return;
        }

        if (!$this->kebutuhanId) return;

        $kebutuhan = Kebutuhan::findOrFail($this->kebutuhanId);
        $kebutuhan->update([
            'status' => 'ditolak',
            'catatan_admin' => $this->catatan_admin_dropdown,
            'tanggal_konfirmasi' => now(),
        ]);

        $this->js("window.dispatchEvent(new CustomEvent('close-modal', { detail: 'reject-modal' }))");
        session()->flash('success', 'Pengajuan kebutuhan telah ditolak dengan catatan resmi.');
    }

    public function deleteKebutuhan(int $id)
    {
        if (!auth()->user()->isAdmin()) return;

        Kebutuhan::findOrFail($id)->delete();
        $this->selectedIds = array_diff($this->selectedIds, [(string)$id]);
        session()->flash('success', 'Pengajuan kebutuhan berhasil dihapus!');
    }

    public function deleteSelectedKebutuhan()
    {
        if (!auth()->user()->isAdmin() || empty($this->selectedIds)) return;

        $count = Kebutuhan::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll = false;
        session()->flash('success', "Berhasil menghapus {$count} data pengajuan sekaligus!");
    }

    public function confirmDelete()
    {
        if ($this->isBulkDelete) {
            $this->deleteSelectedKebutuhan();
            $this->js("window.dispatchEvent(new CustomEvent('close-modal', { detail: 'delete-confirm-modal' }))");
            return;
        }

        if (!$this->deleteTargetId) return;

        $this->deleteKebutuhan($this->deleteTargetId);
        $this->deleteTargetId = null;

        $this->js("window.dispatchEvent(new CustomEvent('close-modal', { detail: 'delete-confirm-modal' }))");
    }

    public function render()
    {
        $user = auth()->user();
        $activePeriode = PeriodeBos::where('is_active', true)->first();

        // Check if current logged-in guru has already submitted in this period
        $hasSubmitted = false;
        if ($user->isGuru() && $activePeriode) {
            $hasSubmitted = Kebutuhan::where('user_id', $user->id)
                ->where('periode_bos_id', $activePeriode->id)
                ->exists();
        }

        $kebutuhans = Kebutuhan::with(['user', 'periodeBos', 'katalogBarang'])
            ->when($user->isGuru(), fn ($q) => $q->where('user_id', $user->id))
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nama_barang', 'like', "%{$this->search}%")
                        ->orWhereHas('katalogBarang', fn ($kb) => $kb->where('nama_barang', 'like', "%{$this->search}%"))
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->orderBy('id', 'desc')
            ->paginate(10);

        $kategoris = Kategori::pluck('nama', 'nama');
        $barangs = $this->kategori_belanja 
            ? KatalogBarang::where('is_active', true)->where('kategori', $this->kategori_belanja)->get()
            : collect();

        $headerTitle = $user->isGuru() ? 'Pengajuan Kebutuhan' : 'Mengelola Kebutuhan';

        return view('livewire.kebutuhan.kebutuhan-manager', compact('kebutuhans', 'kategoris', 'barangs', 'hasSubmitted', 'activePeriode'))
            ->layout('components.layouts.app', ['header' => $headerTitle]);
    }
}
