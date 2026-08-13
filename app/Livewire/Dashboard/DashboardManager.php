<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\PeriodeBos;
use App\Models\Kebutuhan;
use App\Models\Rkas;
use App\Models\Bku;

class DashboardManager extends Component
{
    public function render()
    {
        $activePeriode = PeriodeBos::where('is_active', true)->first();

        $user = auth()->user();

        // Admin / Kepsek KPIs
        $totalPagu = (float) ($activePeriode?->anggaran ?? 0);
        $totalPengajuanKebutuhan = Kebutuhan::when($activePeriode, fn ($q) => $q->where('periode_bos_id', $activePeriode->id))->count();
        $totalRkasAnggaran = (float) Rkas::when($activePeriode, fn ($q) => $q->where('periode_bos_id', $activePeriode->id))->where('status', 'approved')->sum('total_anggaran');
        
        $bku = $activePeriode ? Bku::where('periode_bos_id', $activePeriode->id)->first() : null;
        $totalRealisasiBku = (float) ($bku?->total_pengeluaran ?? 0);
        $sisaPagu = max(0, $totalPagu - $totalRealisasiBku);

        $persentaseRealisasi = $totalPagu > 0 ? round(($totalRealisasiBku / $totalPagu) * 100, 1) : 0;
        $persentaseSisa = $totalPagu > 0 ? round(($sisaPagu / $totalPagu) * 100, 1) : 0;
        $persentaseRkas = $totalPagu > 0 ? round(($totalRkasAnggaran / $totalPagu) * 100, 1) : 0;

        // Guru Specific KPIs
        $guruBaseQuery = Kebutuhan::where('user_id', $user->id)
            ->when($activePeriode, fn ($q) => $q->where('periode_bos_id', $activePeriode->id));

        $totalItemDiajukan = (clone $guruBaseQuery)->count();
        $totalDisetujui = (clone $guruBaseQuery)
            ->whereIn('status', ['diterima', 'disetujui'])
            ->count();
        $totalDitolak = (clone $guruBaseQuery)
            ->where('status', 'ditolak')
            ->count();
        $totalTersedia = (clone $guruBaseQuery)
            ->whereIn('status', ['tersedia', 'dikonfirmasi'])
            ->count();

        $recentKebutuhansQuery = Kebutuhan::with(['user', 'katalogBarang']);
        if ($user->isGuru()) {
            $recentKebutuhansQuery->where('user_id', $user->id);
        }
        $recentKebutuhans = $recentKebutuhansQuery->orderBy('id', 'desc')->take(5)->get();

        $latestRkas = $activePeriode ? Rkas::where('periode_bos_id', $activePeriode->id)->latest()->first() : null;
        $paguInfo = $latestRkas ? $latestRkas->getPaguBreakdown() : null;

        if (!$paguInfo && $activePeriode) {
            $paguInfo = [
                'total_pagu' => $totalPagu,
                'total_rkas' => 0,
                'total_rkas_pct' => 0,
                'total_rkas_status' => 'success',
                'honor' => ['nominal' => 0, 'pct' => 0, 'target' => 'Maksimal 20%', 'status' => 'success', 'message' => 'Dalam Batas Aman (≤ 20%)'],
                'buku' => ['nominal' => 0, 'pct' => 0, 'target' => 'Minimal 20%', 'status' => 'warning', 'message' => 'Belum Memenuhi Minimal 20%'],
                'sarpras' => ['nominal' => 0, 'pct' => 0, 'target' => 'Maksimal 20%', 'status' => 'success', 'message' => 'Dalam Batas Aman (≤ 20%)'],
                'operasional' => ['nominal' => 0, 'pct' => 0, 'target' => 'Fleksibel (± 40%)', 'status' => 'info', 'message' => 'Alokasi Operasional & Kegiatan Sekolah'],
            ];
        }

        return view('livewire.dashboard.dashboard-manager', compact(
            'activePeriode',
            'totalPagu',
            'totalPengajuanKebutuhan',
            'totalRkasAnggaran',
            'totalRealisasiBku',
            'sisaPagu',
            'persentaseRealisasi',
            'persentaseSisa',
            'persentaseRkas',
            'totalItemDiajukan',
            'totalDisetujui',
            'totalDitolak',
            'totalTersedia',
            'recentKebutuhans',
            'paguInfo'
        ))->layout('components.layouts.app', ['header' => 'Beranda']);
    }
}
