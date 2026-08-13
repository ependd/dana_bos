<?php

namespace App\Livewire\Kategori;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Kategori;

class KategoriManager extends Component
{
    use WithPagination;

    public string $search = '';

    // Bulk selection fields
    public array $selectedKategori = [];
    public bool $selectAll = false;

    // Form fields
    public bool $isEdit = false;
    public ?int $kategoriId = null;
    public string $nama = '';
    public string $kelompok = 'Operasional & Kegiatan Sekolah';

    public array $kelompokOptions = [
        'Pembayaran Honorarium',
        'Buku & Perpustakaan',
        'Pemeliharaan Sarana & Prasarana',
        'Operasional & Kegiatan Sekolah',
    ];

    // Delete modal
    public ?int $deleteTargetId = null;
    public ?string $deleteTargetNama = null;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedKategori = Kategori::query()
                ->when($this->search, fn ($q) => $q->where('nama', 'like', "%{$this->search}%")->orWhere('kelompok', 'like', "%{$this->search}%"))
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedKategori = [];
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->selectedKategori = [];
        $this->selectAll = false;
    }

    public function openCreateModal()
    {
        $this->reset(['kategoriId', 'nama', 'kelompok', 'isEdit']);
        $this->kelompok = 'Operasional & Kegiatan Sekolah';
        $this->resetValidation();
        $this->dispatch('open-modal', 'kategori-modal');
    }

    public function openEditModal(int $id)
    {
        $kategori = Kategori::findOrFail($id);
        $this->kategoriId = $kategori->id;
        $this->nama = $kategori->nama;
        $this->kelompok = $kategori->kelompok ?? 'Operasional & Kegiatan Sekolah';
        $this->isEdit = true;
        $this->resetValidation();
        $this->dispatch('open-modal', 'kategori-modal');
    }

    public function save()
    {
        if (!auth()->user()->isAdmin()) {
            session()->flash('error', 'Hanya Admin yang dapat mengelola data kategori.');
            return;
        }

        $this->validate([
            'nama' => 'required|string|max:100|unique:kategoris,nama,' . ($this->kategoriId ?? 'NULL') . ',id',
            'kelompok' => 'required|string|in:' . implode(',', $this->kelompokOptions),
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique' => 'Nama kategori sudah terdaftar.',
            'kelompok.required' => 'Kelompok / bagian kategori wajib dipilih.',
            'kelompok.in' => 'Pilihan kelompok / bagian tidak valid.',
        ]);

        if ($this->isEdit && $this->kategoriId) {
            $kategori = Kategori::findOrFail($this->kategoriId);
            $kategori->update([
                'nama' => $this->nama,
                'kelompok' => $this->kelompok,
            ]);
            session()->flash('success', "Kategori '{$this->nama}' berhasil diperbarui!");
        } else {
            Kategori::create([
                'nama' => $this->nama,
                'kelompok' => $this->kelompok,
            ]);
            session()->flash('success', "Kategori '{$this->nama}' berhasil ditambahkan!");
        }

        $this->dispatch('close-modal', 'kategori-modal');
        $this->reset(['kategoriId', 'nama', 'kelompok', 'isEdit']);
    }

    public function delete(int $id)
    {
        if (!auth()->user()->isAdmin()) return;

        $kategori = Kategori::withCount('katalogBarangs')->findOrFail($id);
        
        if ($kategori->katalog_barangs_count > 0 || $kategori->katalogBarangs()->exists()) {
            $this->dispatch('swal:error', [
                'title' => 'Kategori Berisi Item Barang',
                'text' => "Kategori '{$kategori->nama}' tidak dapat dihapus karena masih terikat dengan {$kategori->katalog_barangs_count} item barang pada Katalog Barang."
            ]);
            session()->flash('error', "Kategori '{$kategori->nama}' tidak dapat dihapus karena masih digunakan oleh item barang.");
            return;
        }

        $kategori->delete();
        $this->selectedKategori = array_diff($this->selectedKategori, [(string)$id]);
        session()->flash('success', "Kategori '{$kategori->nama}' berhasil dihapus!");
    }

    public function deleteSelected()
    {
        if (!auth()->user()->isAdmin() || empty($this->selectedKategori)) return;

        $kategoris = Kategori::withCount('katalogBarangs')->whereIn('id', $this->selectedKategori)->get();
        $cannotDelete = [];
        $deletedCount = 0;

        foreach ($kategoris as $kategori) {
            if ($kategori->katalog_barangs_count > 0 || $kategori->katalogBarangs()->exists()) {
                $cannotDelete[] = $kategori->nama;
            } else {
                $kategori->delete();
                $deletedCount++;
            }
        }

        $this->selectedKategori = [];
        $this->selectAll = false;

        if (count($cannotDelete) > 0 && $deletedCount === 0) {
            $namaList = implode(', ', $cannotDelete);
            $this->dispatch('swal:error', [
                'title' => 'Seluruh Pilihan Berisi Item Barang',
                'text' => "Seluruh pilihan kategori tidak dapat dihapus karena semua kategori yang Anda pilih ({$namaList}) masih memiliki item barang terikat."
            ]);
            session()->flash('error', "Seluruh pilihan kategori ({$namaList}) tidak dapat dihapus karena masih memiliki item barang.");
        } elseif (count($cannotDelete) > 0 && $deletedCount > 0) {
            $namaList = implode(', ', $cannotDelete);
            $this->dispatch('swal:error', [
                'title' => 'Sebagian Kategori Memiliki Item',
                'text' => "Berhasil menghapus {$deletedCount} kategori. Namun, dalam pilihan Anda terdapat kategori ({$namaList}) yang masih memiliki item barang sehingga tidak dapat dihapus."
            ]);
            session()->flash('warning', "Berhasil menghapus {$deletedCount} kategori. Kategori ({$namaList}) tidak dapat dihapus karena masih memiliki item barang.");
        } else {
            session()->flash('success', "Berhasil menghapus seluruh kategori terpilih!");
        }
    }

    public function render()
    {
        $kategoris = Kategori::withCount('katalogBarangs')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nama', 'like', "%{$this->search}%")
                        ->orWhere('kelompok', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('livewire.kategori.kategori-manager', compact('kategoris'))
            ->layout('components.layouts.app', ['header' => 'Kategori']);
    }
}
