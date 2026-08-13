<?php

namespace App\Livewire\PeriodeBos;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PeriodeBos;

class PeriodeBosManager extends Component
{
    use WithPagination;

    public string $search = '';

    // Bulk selection fields
    public array $selectedPeriodes = [];
    public bool $selectAll = false;

    // Form fields
    public bool $isEdit = false;
    public ?int $periodeId = null;
    public string $tahun_ajaran = '';
    public string $tanggal_mulai = '';
    public string $tanggal_selesai = '';
    public $anggaran = null;
    public bool $is_active = true;
    public bool $status_pengajuan = true;

    protected function rules(): array
    {
        return [
            'tahun_ajaran' => 'required|string|max:20|unique:periode_bos,tahun_ajaran,' . ($this->periodeId ?? 'NULL') . ',id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'anggaran' => 'required|numeric|min:1',
            'is_active' => 'boolean',
            'status_pengajuan' => 'boolean',
        ];
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedPeriodes = PeriodeBos::query()
                ->when($this->search, fn ($q) => $q->where('tahun_ajaran', 'like', "%{$this->search}%"))
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedPeriodes = [];
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->selectedPeriodes = [];
        $this->selectAll = false;
    }

    public function openCreateModal()
    {
        $this->reset(['periodeId', 'tahun_ajaran', 'tanggal_mulai', 'tanggal_selesai', 'anggaran', 'is_active', 'status_pengajuan', 'isEdit']);
        $this->anggaran = null;
        $this->is_active = true;
        $this->status_pengajuan = true;
        $this->resetValidation();
        $this->dispatch('open-modal', 'periode-modal');
    }

    public function openEditModal(int $id)
    {
        $periode = PeriodeBos::findOrFail($id);
        $this->periodeId = $periode->id;
        $this->tahun_ajaran = $periode->tahun_ajaran;
        $this->tanggal_mulai = $periode->tanggal_mulai ? $periode->tanggal_mulai->format('Y-m-d') : '';
        $this->tanggal_selesai = $periode->tanggal_selesai ? $periode->tanggal_selesai->format('Y-m-d') : '';
        $this->anggaran = $periode->anggaran;
        $this->is_active = (bool) $periode->is_active;
        $this->status_pengajuan = (bool) ($periode->status_pengajuan ?? true);
        $this->isEdit = true;
        $this->resetValidation();
        $this->dispatch('open-modal', 'periode-modal');
    }

    public function toggleActive(int $id)
    {
        $this->toggleStatus($id);
    }

    public function toggleStatus(int $id)
    {
        if (!auth()->user()->isAdmin()) return;

        $periode = PeriodeBos::findOrFail($id);
        $newStatus = !$periode->is_active;

        if ($newStatus) {
            PeriodeBos::where('id', '!=', $id)->update(['is_active' => false]);
            $periode->update([
                'is_active' => true,
                'status_pengajuan' => true
            ]);
            session()->flash('success', "Periode BOS {$periode->tahun_ajaran} berhasil diaktifkan & disetujui!");
        } else {
            $periode->update([
                'is_active' => false,
                'status_pengajuan' => false
            ]);
            session()->flash('success', "Periode BOS {$periode->tahun_ajaran} diubah menjadi Non-Aktif (Draft).");
        }
    }

    public function save()
    {
        if (!auth()->user()->isAdmin()) return;

        if ($this->anggaran !== null) {
            $this->anggaran = abs((float)preg_replace('/[^0-9.]/', '', (string)$this->anggaran));
        }

        $this->validate();

        if ($this->is_active) {
            PeriodeBos::where('id', '!=', $this->periodeId)->update(['is_active' => false]);
            $this->status_pengajuan = true;
        }

        PeriodeBos::updateOrCreate(
            ['id' => $this->periodeId],
            [
                'tahun_ajaran' => $this->tahun_ajaran,
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
                'anggaran' => $this->anggaran,
                'is_active' => $this->is_active,
                'status_pengajuan' => $this->status_pengajuan,
            ]
        );

        $this->dispatch('close-modal', 'periode-modal');
        session()->flash('success', $this->isEdit ? 'Periode BOS berhasil diperbarui!' : 'Periode BOS baru berhasil ditambahkan!');
    }

    public function delete(int $id)
    {
        if (!auth()->user()->isAdmin()) return;

        $periode = PeriodeBos::findOrFail($id);
        
        $hasData = $periode->rkas()->exists() 
            || $periode->bkus()->exists() 
            || $periode->kebutuhans()->exists() 
            || $periode->hasilKlasifikasis()->exists();

        if ($hasData) {
            $this->dispatch('swal:error', [
                'title' => 'Periode Terikat Data',
                'text' => "Periode BOS {$periode->tahun_ajaran} tidak dapat dihapus karena telah terikat dengan data RKAS, BKU, atau Pengajuan."
            ]);
            session()->flash('error', "Periode BOS {$periode->tahun_ajaran} tidak dapat dihapus karena memiliki data terikat.");
            return;
        }

        $periode->delete();
        $this->selectedPeriodes = array_diff($this->selectedPeriodes, [(string)$id]);
        session()->flash('success', 'Periode BOS berhasil dihapus!');
    }

    public function deleteSelected()
    {
        if (!auth()->user()->isAdmin() || empty($this->selectedPeriodes)) return;

        $periodes = PeriodeBos::whereIn('id', $this->selectedPeriodes)->get();
        $cannotDelete = [];
        $deletedCount = 0;

        foreach ($periodes as $periode) {
            $hasData = $periode->rkas()->exists() 
                || $periode->bkus()->exists() 
                || $periode->kebutuhans()->exists() 
                || $periode->hasilKlasifikasis()->exists();

            if ($hasData) {
                $cannotDelete[] = $periode->tahun_ajaran;
            } else {
                $periode->delete();
                $deletedCount++;
            }
        }

        $this->selectedPeriodes = [];
        $this->selectAll = false;

        if (count($cannotDelete) > 0 && $deletedCount === 0) {
            $namaList = implode(', ', $cannotDelete);
            $this->dispatch('swal:error', [
                'title' => 'Seluruh Pilihan Terikat Data',
                'text' => "Seluruh pilihan periode BOS tidak dapat dihapus karena semua periode yang Anda pilih ({$namaList}) sudah memiliki data terikat."
            ]);
            session()->flash('error', "Seluruh pilihan periode BOS ({$namaList}) tidak dapat dihapus karena memiliki data terikat.");
        } elseif (count($cannotDelete) > 0 && $deletedCount > 0) {
            $namaList = implode(', ', $cannotDelete);
            $this->dispatch('swal:error', [
                'title' => 'Sebagian Periode BOS Terikat Data',
                'text' => "Berhasil menghapus {$deletedCount} periode BOS. Namun, dalam pilihan Anda terdapat periode ({$namaList}) yang sudah memiliki data terikat sehingga tidak dapat dihapus."
            ]);
            session()->flash('warning', "Berhasil menghapus {$deletedCount} periode BOS. Periode ({$namaList}) tidak dapat dihapus karena memiliki data terikat.");
        } else {
            session()->flash('success', "Berhasil menghapus seluruh periode BOS terpilih!");
        }
    }

    public function render()
    {
        $periodes = PeriodeBos::query()
            ->when($this->search, fn ($q) => $q->where('tahun_ajaran', 'like', "%{$this->search}%"))
            ->orderBy('tahun_ajaran', 'desc')
            ->paginate(10);

        return view('livewire.periode-bos.periode-bos-manager', compact('periodes'))
            ->layout('components.layouts.app', ['header' => 'Periode BOS']);
    }
}
