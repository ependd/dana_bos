<?php

namespace App\Livewire\KatalogBarang;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\KatalogBarang;
use App\Models\Kategori;

class KatalogBarangManager extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $barangId = null;

    // Bulk selection fields
    public array $selectedBarangs = [];
    public bool $selectAll = false;

    // Form fields
    public string $kode_barang = '';
    public string $nama_barang = '';
    public string $kategori = '';
    public string $satuan = '';
    public $harga_referensi = null;
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'kode_barang' => 'required|string|max:50|unique:katalog_barangs,kode_barang,' . ($this->barangId ?? 'NULL') . ',id',
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'harga_referensi' => 'required|numeric|min:1',
            'is_active' => 'boolean',
        ];
    }

    public function updatedKategori($value)
    {
        $available = $this->availableSatuans;
        if (!in_array($this->satuan, $available)) {
            $this->satuan = $available[0] ?? '';
        }
    }

    public function getAvailableSatuansProperty(): array
    {
        $kat = strtolower(trim($this->kategori));

        if (empty($kat)) {
            return [
                'Pcs', 'Buah', 'Unit', 'Set', 'Paket', 'Box', 'Rim', 'Buku',
                'Eksemplar', 'Lembar', 'Pak', 'Dus', 'Roll', 'Botol', 'Kaleng',
                'Galon', 'Liter', 'Kg', 'Gram', 'Meter', 'Batang', 'Sak',
                'Kegiatan', 'Kali', 'OH (Orang/Hari)', 'OK (Orang/Kegiatan)'
            ];
        }

        if (str_contains($kat, 'atk') || str_contains($kat, 'tulis') || str_contains($kat, 'kantor')) {
            return ['Pcs', 'Buah', 'Lembar', 'Rim', 'Box', 'Pak', 'Dus', 'Buku', 'Roll', 'Set'];
        }

        if (str_contains($kat, 'bahan ajar') || str_contains($kat, 'buku') || str_contains($kat, 'modul') || str_contains($kat, 'peraga')) {
            return ['Buku', 'Eksemplar', 'Paket', 'Set', 'Pcs', 'Buah', 'Lembar', 'Box'];
        }

        if (str_contains($kat, 'honor') || str_contains($kat, 'gaji') || str_contains($kat, 'upah')) {
            return ['OH (Orang/Hari)', 'OK (Orang/Kegiatan)', 'Kali', 'Kegiatan', 'Bulan', 'Orang'];
        }

        if (str_contains($kat, 'kegiatan') || str_contains($kat, 'acara') || str_contains($kat, 'lomba')) {
            return ['Kegiatan', 'Paket', 'Kali', 'Porsi', 'OH (Orang/Hari)', 'OK (Orang/Kegiatan)', 'Orang'];
        }

        if (str_contains($kat, 'mebel') || str_contains($kat, 'kursi') || str_contains($kat, 'meja')) {
            return ['Unit', 'Set', 'Buah', 'Pcs'];
        }

        if (str_contains($kat, 'pelihara') || str_contains($kat, 'servis') || str_contains($kat, 'perbaikan') || str_contains($kat, 'rawat')) {
            return ['Unit', 'Kegiatan', 'Kali', 'Botol', 'Kaleng', 'Galon', 'Liter', 'Kg', 'Batang', 'Sak', 'Meter', 'Pcs', 'Buah', 'Roll', 'Set'];
        }

        if (str_contains($kat, 'lab') || str_contains($kat, 'praktik') || str_contains($kat, 'sains')) {
            return ['Unit', 'Set', 'Paket', 'Botol', 'Pcs', 'Buah', 'Box', 'Liter', 'Kg', 'Gram'];
        }

        if (str_contains($kat, 'tik') || str_contains($kat, 'komputer') || str_contains($kat, 'laptop') || str_contains($kat, 'elektronik') || str_contains($kat, 'digital')) {
            return ['Unit', 'Set', 'Pcs', 'Buah', 'Paket', 'Box', 'Meter', 'Roll'];
        }

        // Default fallback for custom category
        return ['Pcs', 'Buah', 'Unit', 'Set', 'Paket', 'Box', 'Lembar', 'Rim', 'Botol', 'Liter', 'Kg', 'Meter', 'Kegiatan'];
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedBarangs = KatalogBarang::query()
                ->when($this->search, function ($q) {
                    $q->where('nama_barang', 'like', "%{$this->search}%")
                      ->orWhere('kode_barang', 'like', "%{$this->search}%")
                      ->orWhere('kategori', 'like', "%{$this->search}%");
                })
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedBarangs = [];
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->selectedBarangs = [];
        $this->selectAll = false;
    }

    public function openCreateModal()
    {
        $this->reset(['kode_barang', 'nama_barang', 'kategori', 'satuan', 'harga_referensi', 'is_active', 'barangId']);
        $this->satuan = '';
        $this->is_active = true;
        $this->isEdit = false;
        $this->dispatch('open-modal', 'katalog-modal');
    }

    public function openEditModal(int $id)
    {
        $barang = KatalogBarang::findOrFail($id);
        $this->barangId = $barang->id;
        $this->kode_barang = $barang->kode_barang;
        $this->nama_barang = $barang->nama_barang;
        $this->kategori = $barang->kategori;
        $this->satuan = $barang->satuan;
        $this->harga_referensi = $barang->harga_referensi;
        $this->is_active = (bool) $barang->is_active;

        $this->isEdit = true;
        $this->dispatch('open-modal', 'katalog-modal');
    }

    public function save()
    {
        if (!auth()->user()->isAdmin()) return;

        if ($this->harga_referensi !== null) {
            $this->harga_referensi = abs((int)preg_replace('/[^0-9]/', '', (string)$this->harga_referensi));
        }

        $this->validate();

        KatalogBarang::updateOrCreate(
            ['id' => $this->barangId],
            [
                'kode_barang' => $this->kode_barang,
                'nama_barang' => $this->nama_barang,
                'kategori' => $this->kategori,
                'satuan' => $this->satuan,
                'harga_referensi' => $this->harga_referensi,
                'is_active' => $this->is_active,
            ]
        );

        $this->dispatch('close-modal', 'katalog-modal');
        session()->flash('success', $this->isEdit ? 'Data barang berhasil diperbarui!' : 'Data barang baru berhasil ditambahkan!');
    }

    public function delete(int $id)
    {
        if (!auth()->user()->isAdmin()) return;

        $barang = KatalogBarang::findOrFail($id);

        // Update related kebutuhans to preserve their nama_barang before deleting master item
        \App\Models\Kebutuhan::where('katalog_barang_id', $barang->id)->update([
            'nama_barang' => $barang->nama_barang,
            'katalog_barang_id' => null,
        ]);

        $barang->delete();
        $this->selectedBarangs = array_diff($this->selectedBarangs, [(string)$id]);
        session()->flash('success', 'Data barang berhasil dihapus!');
    }

    public function deleteSelected()
    {
        if (!auth()->user()->isAdmin() || empty($this->selectedBarangs)) return;

        $barangs = KatalogBarang::whereIn('id', $this->selectedBarangs)->get();
        $deletedCount = 0;

        foreach ($barangs as $barang) {
            \App\Models\Kebutuhan::where('katalog_barang_id', $barang->id)->update([
                'nama_barang' => $barang->nama_barang,
                'katalog_barang_id' => null,
            ]);
            $barang->delete();
            $deletedCount++;
        }

        $this->selectedBarangs = [];
        $this->selectAll = false;
        session()->flash('success', "Berhasil menghapus {$deletedCount} data barang terpilih!");
    }

    public function render()
    {
        $barangs = KatalogBarang::query()
            ->when($this->search, function ($q) {
                $q->where('nama_barang', 'like', "%{$this->search}%")
                  ->orWhere('kode_barang', 'like', "%{$this->search}%")
                  ->orWhere('kategori', 'like', "%{$this->search}%");
            })
            ->orderBy('nama_barang', 'asc')
            ->paginate(12);

        $kategoriList = Kategori::orderBy('nama', 'asc')->get();

        return view('livewire.katalog-barang.katalog-barang-manager', compact('barangs', 'kategoriList'))
            ->layout('components.layouts.app', ['header' => 'Barang']);
    }
}
