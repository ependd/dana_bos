<?php

namespace App\Livewire\Klasifikasi;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\HasilKlasifikasi;
use App\Models\PeriodeBos;
use App\Services\ClassificationOrchestrator;

class KlasifikasiManager extends Component
{
    use WithPagination;

    public ?int $selected_periode = null;
    public string $selected_model = 'woa'; // woa or baseline
    public ?array $classificationResult = null;
    public ?array $evaluationResult = null;
    public array $selectedWoaFeatures = [];
    public bool $hasRunClassification = false;
    public string $activeMode = 'classification';

    public function mount()
    {
        $this->selected_periode = PeriodeBos::where('is_active', true)->first()?->id
                               ?? PeriodeBos::first()?->id;
    }

    public function updatedSelectedPeriode()
    {
        $this->hasRunClassification = false;
        $this->resetPage();
    }

    public function updatedSelectedModel()
    {
        $this->hasRunClassification = false;
        $this->resetPage();
    }

    public function runClassification()
    {
        if (!$this->selected_periode) {
            session()->flash('error', 'Pilih periode anggaran terlebih dahulu.');
            return;
        }

        try {
            $orchestrator = new ClassificationOrchestrator();
            $this->classificationResult = $orchestrator->classify($this->selected_periode);
            $this->evaluationResult = $orchestrator->evaluate(5);
            $this->selectedWoaFeatures = $this->classificationResult['woa']['features'] ?? [];
            $this->hasRunClassification = true;
            $this->activeMode = 'classification';

            session()->flash('success', "Klasifikasi Selesai! Total: {$this->classificationResult['total']} Data.");
        } catch (\Exception $e) {
            session()->flash('error', 'Klasifikasi Gagal: ' . $e->getMessage());
        }
    }

    public function runEvaluation()
    {
        try {
            $orchestrator = new ClassificationOrchestrator();
            $this->evaluationResult = $orchestrator->evaluate(5);
            $this->selectedWoaFeatures = $this->evaluationResult['woa']['features'] ?? [];
            $this->hasRunClassification = false;
            $this->activeMode = 'evaluation';

            session()->flash('success', "Evaluasi 5-Fold Cross Validation Selesai! Akurasi Naive Bayes + WOA: {$this->evaluationResult['woa']['average']['accuracy']}%");
        } catch (\Exception $e) {
            session()->flash('error', 'Evaluasi Gagal: ' . $e->getMessage());
        }
    }

    public function isFeatureActive(string $featureCode): bool
    {
        if ($this->selected_model === 'baseline') {
            return true;
        }

        $activeFeatures = !empty($this->selectedWoaFeatures)
            ? $this->selectedWoaFeatures
            : ($this->evaluationResult['woa']['features'] ?? ['X3', 'X4', 'X5', 'X6', 'X7']);

        return in_array(strtoupper($featureCode), array_map('strtoupper', $activeFeatures));
    }

    public function formatNormalized(float $value, float $min, float $max): string
    {
        $range = $max - $min;
        if ($range <= 0) {
            return '0.0000';
        }
        $norm = ($value - $min) / $range;
        $normBounded = max(0, min(1, $norm));
        return number_format($normBounded, 4);
    }

    public function render()
    {
        $periodes = PeriodeBos::pluck('tahun_ajaran', 'id');

        $query = HasilKlasifikasi::query()
            ->when($this->selected_periode, fn ($q) => $q->where('periode_bos_id', $this->selected_periode)->where('metode', $this->selected_model))
            ->when(!$this->selected_periode, fn ($q) => $q->whereRaw('1 = 0'));

        $trainingData = \App\Models\DataTraining::all();
        if ($trainingData->isNotEmpty()) {
            $minMax = [
                'x2' => ['min' => (float)($trainingData->min('x2_standar_komponen_s') ?? 1), 'max' => (float)($trainingData->max('x2_standar_komponen_s') ?? 8)],
                'x3' => ['min' => (float)($trainingData->min('x3_standar_komponen_k') ?? 1), 'max' => (float)($trainingData->max('x3_standar_komponen_k') ?? 12)],
                'x4' => ['min' => (float)($trainingData->min('x4_jumlah_anggaran') ?? 0), 'max' => (float)($trainingData->max('x4_jumlah_anggaran') ?? 1)],
                'x5' => ['min' => (float)($trainingData->min('x5_pengeluaran') ?? 0), 'max' => (float)($trainingData->max('x5_pengeluaran') ?? 1)],
                'x6' => ['min' => (float)($trainingData->min('x6_selisih') ?? 0), 'max' => (float)($trainingData->max('x6_selisih') ?? 1)],
                'x7' => ['min' => (float)($trainingData->min('x7_persentase_realisasi') ?? 0), 'max' => (float)($trainingData->max('x7_persentase_realisasi') ?? 100)],
            ];
        } else {
            $allData = (clone $query)->get();
            $minMax = [
                'x2' => ['min' => (float)($allData->min('x2_standar_komponen_s') ?? 1), 'max' => (float)($allData->max('x2_standar_komponen_s') ?? 8)],
                'x3' => ['min' => (float)($allData->min('x3_standar_komponen_k') ?? 1), 'max' => (float)($allData->max('x3_standar_komponen_k') ?? 12)],
                'x4' => ['min' => (float)($allData->min('x4_jumlah_anggaran') ?? 0), 'max' => (float)($allData->max('x4_jumlah_anggaran') ?? 1)],
                'x5' => ['min' => (float)($allData->min('x5_pengeluaran') ?? 0), 'max' => (float)($allData->max('x5_pengeluaran') ?? 1)],
                'x6' => ['min' => (float)($allData->min('x6_selisih') ?? 0), 'max' => (float)($allData->max('x6_selisih') ?? 1)],
                'x7' => ['min' => (float)($allData->min('x7_persentase_realisasi') ?? 0), 'max' => (float)($allData->max('x7_persentase_realisasi') ?? 100)],
            ];
        }

        $results = $query->orderBy('kode_rekening', 'asc')->paginate(15);

        return view('livewire.klasifikasi.klasifikasi-manager', compact('periodes', 'results', 'minMax'))
            ->layout('components.layouts.app', ['header' => 'Klasifikasi']);
    }
}
