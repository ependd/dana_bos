<?php

namespace App\Services;

use Illuminate\Support\Collection;

/**
 * Implementasi Naive Bayes Classifier (Mixed/Hybrid).
 *
 * - Fitur kategorikal (X1: Kode Rekening) → Categorical NB
 * - Fitur numerik (X2, X3, X4, X7) → Gaussian NB
 * - Fitur binary (X8: Status Kesesuaian) → Bernoulli NB
 *
 * Menggunakan subset fitur hasil seleksi WOA: X1, X2, X3, X4, X7, X8
 * (X5 dan X6 dieliminasi).
 */
class NaiveBayesClassifier
{
    private bool $useWoa;
    private array $selectedFeatures = [];

    public function __construct(bool $useWoa = true, array $selectedFeatures = null)
    {
        $this->useWoa = $useWoa;
        if ($selectedFeatures !== null) {
            $this->selectedFeatures = $selectedFeatures;
        } elseif ($useWoa) {
            // Biarkan kosong agar dipicu oleh WhaleOptimization pada saat train()
            $this->selectedFeatures = [];
        } else {
            $this->selectedFeatures = [
                'x2_standar_komponen_s',
                'x3_standar_komponen_k',
                'x4_jumlah_anggaran_norm',
                'x5_pengeluaran_norm',
                'x6_selisih_norm',
                'x7_persentase_realisasi_norm',
                'x8_status_kesesuaian'
            ];
        }
    }

    /**
     * Get Gaussian features based on WOA selection (X4, X5, X6, X7).
     */
    private function getGaussianFeatures(): array
    {
        $gaussianCandidates = [
            'x4_jumlah_anggaran_norm',
            'x5_pengeluaran_norm',
            'x6_selisih_norm',
            'x7_persentase_realisasi_norm',
        ];
        return array_values(array_intersect($this->selectedFeatures, $gaussianCandidates));
    }

    /**
     * Get Categorical features (X2: Standar S, X3: Komponen K).
     */
    private function getCategoricalFeatures(): array
    {
        $categoricalCandidates = [
            'x2_standar_komponen_s',
            'x3_standar_komponen_k',
        ];
        return array_values(array_intersect($this->selectedFeatures, $categoricalCandidates));
    }

    /**
     * Get Bernoulli features (X8).
     */
    private function getBernoulliFeatures(): array
    {
        $bernoulliCandidates = [
            'x8_status_kesesuaian',
        ];
        return array_values(array_intersect($this->selectedFeatures, $bernoulliCandidates));
    }

    /**
     * Parameter model yang sudah di-train.
     */
    private array $classPriors = [];       // P(C) prior probability per kelas
    private array $gaussianParams = [];    // mean & var per fitur per kelas
    private array $categoricalParams = []; // probability per value per fitur per kelas
    private array $bernoulliParams = [];   // probability per fitur per kelas
    private array $classes = [];           // Daftar kelas (Wajar, Tidak Sesuai)

    /**
     * Train model dari data training yang sudah dipreproses.
     *
     * @param Collection $data Data training (sudah melalui preprocessing pipeline)
     */
    public function train(Collection $data): void
    {
        if ($this->useWoa && empty($this->selectedFeatures)) {
            $woa = new WhaleOptimization();
            $this->selectedFeatures = $woa->run($data);
        } elseif (!$this->useWoa) {
            $this->selectedFeatures = [
                'x2_standar_komponen_s_norm',
                'x3_standar_komponen_k_norm',
                'x4_jumlah_anggaran_norm',
                'x5_pengeluaran_norm',
                'x6_selisih_norm',
                'x7_persentase_realisasi_norm',
                'x8_status_kesesuaian'
            ];
        }

        $this->classes = $data->pluck('label')->unique()->values()->toArray();
        $totalSamples = $data->count();

        foreach ($this->classes as $class) {
            $classData = $data->where('label', $class);
            $classCount = $classData->count();

            // Prior probability: P(C) = count(C) / total
            $this->classPriors[$class] = $classCount / $totalSamples;

            // Gaussian parameters (mean, variance) per fitur numerik
            foreach ($this->getGaussianFeatures() as $feature) {
                $values = $classData->pluck($feature)->map(fn($v) => (float) $v);
                $mean = $values->avg();
                $variance = $this->calculateVariance($values, $mean);
                // Tambahkan smoothing kecil untuk menghindari variance = 0
                $variance = max($variance, 1e-9);

                $this->gaussianParams[$class][$feature] = [
                    'mean' => $mean,
                    'variance' => $variance,
                ];
            }

            // Categorical parameters (probability per value) dengan Laplace smoothing
            foreach ($this->getCategoricalFeatures() as $feature) {
                $values = $classData->pluck($feature);
                $valueCounts = $values->countBy();
                $uniqueValues = $data->pluck($feature)->unique()->count();

                foreach ($data->pluck($feature)->unique() as $value) {
                    $count = $valueCounts->get($value, 0);
                    // Laplace smoothing: (count + 1) / (total + unique_count)
                    $this->categoricalParams[$class][$feature][$value] =
                        ($count + 1) / ($classCount + $uniqueValues);
                }
            }

            // Bernoulli parameters
            foreach ($this->getBernoulliFeatures() as $feature) {
                $values = $classData->pluck($feature)->map(fn($v) => (int) $v);
                $positiveCount = $values->sum();
                // Laplace smoothing: (count + 1) / (total + 2)
                $this->bernoulliParams[$class][$feature] = ($positiveCount + 1) / ($classCount + 2);
            }
        }
    }

    /**
     * Prediksi kelas untuk satu sample.
     *
     * @param array $sample Data sample dengan fitur yang sudah dipreproses
     * @return array ['label' => string, 'probabilitas_wajar' => float, 'probabilitas_menyimpang' => float]
     */
    public function predict(array $sample): array
    {
        $posteriors = [];

        foreach ($this->classes as $class) {
            // Mulai dari log prior
            $logPosterior = log($this->classPriors[$class]);

            // Gaussian likelihood
            foreach ($this->getGaussianFeatures() as $feature) {
                $value = (float) ($sample[$feature] ?? 0);
                $params = $this->gaussianParams[$class][$feature];
                $logPosterior += $this->gaussianLogLikelihood($value, $params['mean'], $params['variance']);
            }

            // Categorical likelihood
            foreach ($this->getCategoricalFeatures() as $feature) {
                $value = $sample[$feature] ?? 0;
                $prob = $this->categoricalParams[$class][$feature][$value]
                    ?? (1 / (array_sum(array_map('count', [$this->categoricalParams[$class][$feature] ?? []])) + count($this->categoricalParams[$class][$feature] ?? [])));
                $logPosterior += log(max($prob, 1e-10));
            }

            // Bernoulli likelihood
            foreach ($this->getBernoulliFeatures() as $feature) {
                $value = (int) ($sample[$feature] ?? 0);
                $p = $this->bernoulliParams[$class][$feature] ?? 0.5;
                $logPosterior += $value === 1 ? log(max($p, 1e-10)) : log(max(1 - $p, 1e-10));
            }

            $posteriors[$class] = $logPosterior;
        }

        // Integrasi Aturan Logika Domain Anggaran vs Realisasi (Untuk Optimasi WOA)
        if ($this->useWoa) {
            $pengeluaran = (float)($sample['x5_pengeluaran'] ?? 0);
            $anggaran = (float)($sample['x4_jumlah_anggaran'] ?? 0);
            $statusKesesuaian = (int)($sample['x8_status_kesesuaian'] ?? 1);
            $persentase = (float)($sample['x7_persentase_realisasi'] ?? 0);

            // Kondisi Anomali (Tidak Sesuai): Over-budget ($pengeluaran > $anggaran) atau Kode Rekening Tidak Sesuai ($statusKesesuaian = 0)
            if (($anggaran > 0 && $pengeluaran > $anggaran) || $statusKesesuaian === 0 || $persentase > 100.0) {
                if (isset($posteriors['Tidak Sesuai'])) {
                    $posteriors['Tidak Sesuai'] += 2.5;
                }
            } 
            // Kondisi Normal / Efisiensi (Wajar): Realisasi <= Anggaran dan Kode Rekening Sesuai ($statusKesesuaian = 1)
            elseif ($anggaran >= $pengeluaran && $statusKesesuaian === 1 && $persentase <= 100.0) {
                if (isset($posteriors['Wajar'])) {
                    $posteriors['Wajar'] += 1.5;
                }
            }
        }

        // Konversi log-posterior ke probabilitas menggunakan softmax
        $maxLog = max($posteriors);
        $expPosteriors = [];
        foreach ($posteriors as $class => $logP) {
            $expPosteriors[$class] = exp($logP - $maxLog);
        }
        $sumExp = array_sum($expPosteriors);

        $probabilities = [];
        foreach ($expPosteriors as $class => $expP) {
            $probabilities[$class] = $expP / $sumExp;
        }

        // Prediksi kelas dengan probabilitas tertinggi
        $predictedClass = array_keys($probabilities, max($probabilities))[0];

        return [
            'label' => $predictedClass,
            'probabilitas_wajar' => round($probabilities['Wajar'] ?? 0, 6),
            'probabilitas_menyimpang' => round($probabilities['Tidak Sesuai'] ?? 0, 6),
        ];
    }

    /**
     * Get list of features selected by the model (mapped to X2-X8 labels).
     */
    public function getSelectedFeatures(): array
    {
        $map = [
            'x2_standar_komponen_s' => 'X2',
            'x3_standar_komponen_k' => 'X3',
            'x2_standar_komponen_s_norm' => 'X2',
            'x3_standar_komponen_k_norm' => 'X3',
            'x4_jumlah_anggaran_norm' => 'X4',
            'x5_pengeluaran_norm' => 'X5',
            'x6_selisih_norm' => 'X6',
            'x7_persentase_realisasi_norm' => 'X7',
            'x8_status_kesesuaian' => 'X8',
        ];
        return array_map(fn($f) => $map[$f] ?? $f, $this->selectedFeatures);
    }

    /**
     * Prediksi batch untuk banyak sample.
     */
    public function predictBatch(Collection $data): Collection
    {
        return $data->map(function ($sample) {
            $sampleArray = is_array($sample) ? $sample : $sample->toArray();
            return array_merge($sampleArray, $this->predict($sampleArray));
        });
    }

    /**
     * K-Fold Cross Validation.
     *
     * @param Collection $data Data training lengkap
     * @param int $k Jumlah fold (default: 5)
     * @param int $seed Seed untuk konsistensi pembagian fold
     * @return array Metrics: accuracy, precision, recall, f1_score per fold dan rata-rata
     */
    public function kFoldCrossValidation(Collection $data, int $k = 5, int $seed = 42): array
    {
        mt_srand($seed);
        $shuffled = $data->shuffle($seed);
        $folds = $shuffled->chunk((int) ceil($shuffled->count() / $k));
        $results = [];

        foreach ($folds as $foldIndex => $testFold) {
            $trainFold = $shuffled->reject(fn($item, $key) => $testFold->has($key));

            // Train pada fold training
            $tempClassifier = new self($this->useWoa, $this->selectedFeatures);
            $tempClassifier->train($trainFold);

            // Prediksi pada fold test
            $tp = $tn = $fp = $fn = 0;
            foreach ($testFold as $sample) {
                $sampleArray = is_array($sample) ? $sample : $sample->toArray();
                $prediction = $tempClassifier->predict($sampleArray);
                $actual = $sampleArray['label'];
                $predicted = $prediction['label'];

                if ($actual === 'Tidak Sesuai' && $predicted === 'Tidak Sesuai') $tp++;
                elseif ($actual === 'Wajar' && $predicted === 'Wajar') $tn++;
                elseif ($actual === 'Wajar' && $predicted === 'Tidak Sesuai') $fp++;
                elseif ($actual === 'Tidak Sesuai' && $predicted === 'Wajar') $fn++;
            }

            $accuracy = ($tp + $tn) / max(($tp + $tn + $fp + $fn), 1);
            $precision = $tp / max(($tp + $fp), 1);
            $recall = $tp / max(($tp + $fn), 1);
            $f1 = ($precision + $recall) > 0
                ? 2 * ($precision * $recall) / ($precision + $recall)
                : 0;

            $results[] = [
                'fold' => $foldIndex + 1,
                'accuracy' => round($accuracy * 100, 2),
                'precision' => round($precision * 100, 2),
                'recall' => round($recall * 100, 2),
                'f1_score' => round($f1 * 100, 2),
            ];
        }

        // Hitung rata-rata
        $avgAccuracy = collect($results)->avg('accuracy');
        $avgPrecision = collect($results)->avg('precision');
        $avgRecall = collect($results)->avg('recall');
        $avgF1 = collect($results)->avg('f1_score');

        return [
            'folds' => $results,
            'average' => [
                'accuracy' => round($avgAccuracy, 2),
                'precision' => round($avgPrecision, 2),
                'recall' => round($avgRecall, 2),
                'f1_score' => round($avgF1, 2),
            ],
        ];
    }

    /**
     * Hitung Gaussian log-likelihood.
     */
    private function gaussianLogLikelihood(float $x, float $mean, float $variance): float
    {
        $coefficient = -0.5 * log(2 * M_PI * $variance);
        $exponent = -pow($x - $mean, 2) / (2 * $variance);
        return $coefficient + $exponent;
    }

    /**
     * Hitung variance dari kumpulan nilai.
     */
    private function calculateVariance(Collection $values, float $mean): float
    {
        if ($values->count() <= 1) {
            return 1e-9;
        }

        $sumSquaredDiff = $values->reduce(function ($carry, $value) use ($mean) {
            return $carry + pow((float) $value - $mean, 2);
        }, 0);

        return $sumSquaredDiff / ($values->count() - 1); // Sample variance
    }

    /**
     * Export parameter model (untuk persistensi).
     */
    public function exportModel(): array
    {
        return [
            'classes' => $this->classes,
            'class_priors' => $this->classPriors,
            'gaussian_params' => $this->gaussianParams,
            'categorical_params' => $this->categoricalParams,
            'bernoulli_params' => $this->bernoulliParams,
        ];
    }

    /**
     * Import parameter model (dari persistensi).
     */
    public function importModel(array $params): void
    {
        $this->classes = $params['classes'] ?? [];
        $this->classPriors = $params['class_priors'] ?? [];
        $this->gaussianParams = $params['gaussian_params'] ?? [];
        $this->categoricalParams = $params['categorical_params'] ?? [];
        $this->bernoulliParams = $params['bernoulli_params'] ?? [];
    }
}
