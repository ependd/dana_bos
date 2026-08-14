<?php

namespace App\Livewire\Monitoring;

use Livewire\Component;
use App\Models\PeriodeBos;
use App\Models\Kebutuhan;
use App\Models\Rkas;
use App\Models\Bku;
use App\Models\BkuItem;

class MonitoringManager extends Component
{
    public ?int $selected_periode = null;

    public function mount()
    {
        if (auth()->user()?->isAdmin()) {
            abort(403, 'Akses Ditolak. Halaman Monitoring Anggaran tidak tersedia untuk akun Admin.');
        }

        $this->selected_periode = PeriodeBos::where('is_active', true)->first()?->id
                               ?? PeriodeBos::first()?->id;
    }

    public function render()
    {
        $periodes = PeriodeBos::pluck('tahun_ajaran', 'id');
        $activePeriode = $this->selected_periode ? PeriodeBos::find($this->selected_periode) : null;

        $totalPagu = $activePeriode?->anggaran ?? 0;

        $bku = $activePeriode ? Bku::where('periode_bos_id', $activePeriode->id)->first() : null;
        $totalPengeluaran = $bku?->total_pengeluaran ?? 0;
        $sisaKas = $totalPagu - $totalPengeluaran;

        $persentaseRealisasi = $totalPagu > 0 ? min(100, round(($totalPengeluaran / $totalPagu) * 100, 2)) : 0;

        // Breakdown per kategori belanja
        $breakdownKategori = $bku ? BkuItem::with('rkasItem')
            ->where('bku_id', $bku->id)
            ->get()
            ->groupBy(fn ($item) => $item->rkasItem?->kebutuhan?->kategori_belanja ?? 'Lain-lain')
            ->map(fn ($group) => [
                'total' => $group->sum('pengeluaran'),
                'count' => $group->count(),
            ]) : collect();

        return view('livewire.monitoring.monitoring-manager', compact(
            'periodes',
            'activePeriode',
            'totalPagu',
            'totalPengeluaran',
            'sisaKas',
            'persentaseRealisasi',
            'breakdownKategori'
        ))->layout('components.layouts.app', ['header' => 'Monitoring Anggaran']);
    }
}
