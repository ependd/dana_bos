<?php

namespace App\Services;

use Illuminate\Support\Collection;

/**
 * Whale Optimization Algorithm (WOA) untuk Seleksi Fitur.
 * Bertujuan mencari kombinasi fitur X2-X8 terbaik yang memaksimalkan akurasi klasifikasi.
 */
class WhaleOptimization
{
    private array $candidates = [
        'x2_standar_komponen_s',
        'x3_standar_komponen_k',
        'x4_jumlah_anggaran_norm',
        'x5_pengeluaran_norm',
        'x6_selisih_norm',
        'x7_persentase_realisasi_norm',
        'x8_status_kesesuaian'
    ];

    private int $numWhales = 15;      // Jumlah agen pencari (Paus)
    private int $maxIterations = 15;  // Maksimum iterasi

    /**
     * Jalankan WOA untuk menyeleksi fitur terbaik dari data training.
     *
     * @param Collection $data Data training preprocessed
     * @return array Daftar nama fitur terpilih
     */
    public function run(Collection $data): array
    {
        mt_srand(42);
        $d = count($this->candidates);

        // Pre-convert collection to raw array for ultra-fast performance
        $rawArray = $data->map(function ($item) {
            return is_array($item) ? $item : $item->toArray();
        })->toArray();

        // Pre-split 3-Fold Cross Validation once
        $k = 3;
        shuffle($rawArray);
        $total = count($rawArray);
        $chunkSize = (int) ceil($total / $k);
        $folds = array_chunk($rawArray, max(1, $chunkSize));

        $precomputedFolds = [];
        foreach ($folds as $foldIdx => $testFold) {
            $trainFold = [];
            foreach ($folds as $otherIdx => $otherFold) {
                if ($otherIdx !== $foldIdx) {
                    foreach ($otherFold as $item) {
                        $trainFold[] = $item;
                    }
                }
            }
            $precomputedFolds[] = [
                'train' => $trainFold,
                'test' => $testFold,
            ];
        }
        
        // 1. Inisialisasi posisi paus secara acak
        $positions = [];
        for ($i = 0; $i < $this->numWhales; $i++) {
            $positions[$i] = [];
            for ($j = 0; $j < $d; $j++) {
                $positions[$i][$j] = mt_rand(-500, 500) / 100;
            }
        }

        $bestFitness = -1.0;
        $bestPosition = [];
        $bestFeatures = [];

        // 2. Loop Utama WOA
        for ($t = 0; $t < $this->maxIterations; $t++) {
            $a = 2 - $t * (2 / $this->maxIterations);

            for ($i = 0; $i < $this->numWhales; $i++) {
                $selectedFeatures = [];
                for ($j = 0; $j < $d; $j++) {
                    $sig = 1 / (1 + exp(-$positions[$i][$j]));
                    if ((mt_rand(0, 1000) / 1000) < $sig) {
                        $selectedFeatures[] = $this->candidates[$j];
                    }
                }

                $fitness = $this->calculateFitnessOptimized($precomputedFolds, $selectedFeatures);

                if ($fitness > $bestFitness) {
                    $bestFitness = $fitness;
                    $bestPosition = $positions[$i];
                    $bestFeatures = $selectedFeatures;
                }
            }

            for ($i = 0; $i < $this->numWhales; $i++) {
                $r1 = mt_rand(0, 1000) / 1000;
                $r2 = mt_rand(0, 1000) / 1000;
                $A = 2 * $a * $r1 - $a;
                $C = 2 * $r2;

                $p = mt_rand(0, 1000) / 1000;
                $l = (mt_rand(-1000, 1000) / 1000);

                if ($p < 0.5) {
                    if (abs($A) < 1) {
                        for ($j = 0; $j < $d; $j++) {
                            $D_best = abs($C * $bestPosition[$j] - $positions[$i][$j]);
                            $positions[$i][$j] = $bestPosition[$j] - $A * $D_best;
                        }
                    } else {
                        $randIndex = mt_rand(0, count($positions) - 1);
                        $randWhale = $positions[$randIndex];
                        for ($j = 0; $j < $d; $j++) {
                            $D_rand = abs($C * $randWhale[$j] - $positions[$i][$j]);
                            $positions[$i][$j] = $randWhale[$j] - $A * $D_rand;
                        }
                    }
                } else {
                    for ($j = 0; $j < $d; $j++) {
                        $D_best = abs($bestPosition[$j] - $positions[$i][$j]);
                        $positions[$i][$j] = $D_best * exp($l) * cos(2 * M_PI * $l) + $bestPosition[$j];
                    }
                }

                for ($j = 0; $j < $d; $j++) {
                    $positions[$i][$j] = max(-10.0, min(10.0, $positions[$i][$j]));
                }
            }
        }

        if (empty($bestFeatures) || count($bestFeatures) >= count($this->candidates)) {
            $bestFeatures = [
                'x3_standar_komponen_k',
                'x4_jumlah_anggaran_norm',
                'x5_pengeluaran_norm',
                'x6_selisih_norm',
                'x7_persentase_realisasi_norm',
            ];
        }

        return $bestFeatures;
    }

    private function calculateFitnessOptimized(array $folds, array $features): float
    {
        $featureCount = count($features);
        $totalFeatures = count($this->candidates);

        // Syarat mutlak: Jumlah fitur WOA HARUS LEBIH SEDIKIT dari total fitur Baseline (kurang dari 7)
        if ($featureCount >= $totalFeatures || $featureCount < 2) {
            return 0.0;
        }

        $accuracies = [];
        $classes = ['Wajar', 'Tidak Sesuai'];

        foreach ($folds as $fold) {
            $trainFold = $fold['train'];
            $testFold = $fold['test'];
            $totalTrain = count($trainFold);

            $classPriors = [];
            $gaussianParams = [];
            $bernoulliParams = [];
            $categoricalParams = [];

            foreach ($classes as $class) {
                $classItems = [];
                foreach ($trainFold as $item) {
                    if (($item['label'] ?? '') === $class) {
                        $classItems[] = $item;
                    }
                }
                $classCount = count($classItems);
                $classPriors[$class] = ($classCount + 1) / ($totalTrain + 2);

                foreach ($features as $feature) {
                    if ($feature === 'x8_status_kesesuaian') {
                        $pos = 0;
                        foreach ($classItems as $ci) {
                            $pos += (int)($ci[$feature] ?? 0);
                        }
                        $bernoulliParams[$class][$feature] = ($pos + 1) / ($classCount + 2);
                    } elseif ($feature === 'x2_standar_komponen_s' || $feature === 'x3_standar_komponen_k') {
                        $valCounts = [];
                        foreach ($classItems as $ci) {
                            $v = (string)($ci[$feature] ?? '');
                            $valCounts[$v] = ($valCounts[$v] ?? 0) + 1;
                        }
                        $numCategories = $feature === 'x2_standar_komponen_s' ? 8 : 12;
                        $categoricalParams[$class][$feature] = [
                            'counts' => $valCounts,
                            'class_count' => $classCount,
                            'num_cat' => $numCategories,
                        ];
                    } else {
                        $sum = 0.0;
                        foreach ($classItems as $ci) {
                            $sum += (float)($ci[$feature] ?? 0);
                        }
                        $mean = $classCount > 0 ? $sum / $classCount : 0.0;

                        $sqDiff = 0.0;
                        foreach ($classItems as $ci) {
                            $sqDiff += pow(((float)($ci[$feature] ?? 0)) - $mean, 2);
                        }
                        $variance = $classCount > 1 ? $sqDiff / ($classCount - 1) : 1e-4;
                        $gaussianParams[$class][$feature] = [
                            'mean' => $mean,
                            'variance' => max($variance, 1e-4)
                        ];
                    }
                }
            }

            $tp = $tn = $fp = $fn = 0;
            foreach ($testFold as $sample) {
                $posteriors = [];

                foreach ($classes as $class) {
                    $logPosterior = log($classPriors[$class]);

                    foreach ($features as $feature) {
                        if ($feature === 'x8_status_kesesuaian') {
                            $val = (int)($sample[$feature] ?? 0);
                            $p = $bernoulliParams[$class][$feature] ?? 0.5;
                            $logPosterior += $val === 1 ? log(max($p, 1e-10)) : log(max(1 - $p, 1e-10));
                        } elseif ($feature === 'x2_standar_komponen_s' || $feature === 'x3_standar_komponen_k') {
                            $val = (string)($sample[$feature] ?? '');
                            $cp = $categoricalParams[$class][$feature] ?? ['counts' => [], 'class_count' => 0, 'num_cat' => 6];
                            $cnt = $cp['counts'][$val] ?? 0;
                            $prob = ($cnt + 1) / ($cp['class_count'] + $cp['num_cat']);
                            $logPosterior += log(max($prob, 1e-10));
                        } else {
                            $val = (float)($sample[$feature] ?? 0);
                            $p = $gaussianParams[$class][$feature];
                            $coefficient = -0.5 * log(2 * M_PI * $p['variance']);
                            $exponent = -pow($val - $p['mean'], 2) / (2 * $p['variance']);
                            $logPosterior += ($coefficient + $exponent);
                        }
                    }
                    $posteriors[$class] = $logPosterior;
                }

                $predicted = array_search(max($posteriors), $posteriors);
                $actual = $sample['label'] ?? '';

                $isAnomalyActual = (strcasecmp((string)$actual, 'Tidak Sesuai') === 0 || strcasecmp((string)$actual, 'Menyimpang') === 0);
                $isAnomalyPred = (strcasecmp((string)$predicted, 'Tidak Sesuai') === 0 || strcasecmp((string)$predicted, 'Menyimpang') === 0);

                if ($isAnomalyActual && $isAnomalyPred) $tp++;
                elseif (!$isAnomalyActual && !$isAnomalyPred) $tn++;
                elseif (!$isAnomalyActual && $isAnomalyPred) $fp++;
                elseif ($isAnomalyActual && !$isAnomalyPred) $fn++;
            }

            $accuracies[] = ($tp + $tn) / max(($tp + $tn + $fp + $fn), 1);
        }

        $avgAccuracy = array_sum($accuracies) / count($accuracies);
        $featureCount = count($features);
        $totalFeatures = count($this->candidates);

        return 0.99 * $avgAccuracy + 0.01 * (1 - $featureCount / $totalFeatures);
    }
}
