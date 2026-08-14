<?php

namespace App\Services;

use App\Models\DataTraining;
use App\Models\DataTrainingBku;
use App\Models\DataTrainingRkas;
use Illuminate\Support\Collection;

/**
 * Pipeline preprocessing data untuk klasifikasi Naive Bayes.
 *
 * Tahapan:
 * 1. Pembersihan data (handle missing values)
 * 2. Join RKAS + BKU berdasarkan kode_rekening
 * 3. Feature engineering (hitung X1-X8)
 * 4. Label encoding untuk fitur kategorikal
 * 5. Normalisasi min-max untuk fitur numerik
 */
class PreprocessingPipeline
{
    private const ENCODING_KODE_REKENING = [
        '5.2.1.01.06' => 1,
        '5.2.1.04.02' => 2,
        '5.2.2.01.01' => 3,
        '5.2.2.01.05' => 4,
        '5.2.2.06.01' => 5,
        '5.2.2.11.02' => 6,
        '5.2.3.28.14' => 7,
        '5.2.3.28.27' => 8,
        '5.2.3.11.08' => 9,
        '5.2.3.12.18' => 10,
    ];

    private const ENCODING_KOMPONEN_S = [
        'Standar Isi' => 1,
        'Standar Proses' => 2,
        'Standar Kompetensi Lulusan' => 3,
        'Standar Pendidik dan Tenaga Kependidikan' => 4,
        'Standar Sarana dan Prasarana' => 5,
        'Standar Pengelolaan' => 6,
        'Standar Pembiayaan' => 7,
        'Standar Penilaian Pendidikan' => 8,
    ];

    private const ENCODING_KOMPONEN_K = [
        'Penerimaan Peserta Didik Baru' => 1,
        'Pengembangan Perpustakaan' => 2,
        'Pelaksanaan Kegiatan Pembelajaran dan Ekstrakurikuler' => 3,
        'Pelaksanaan Asesmen dan Evaluasi Pembelajaran' => 4,
        'Pelaksanaan Administrasi Kegiatan Sekolah' => 5,
        'Pengembangan Profesi Guru dan Tenaga Kependidikan' => 6,
        'Pembiayaan Langganan Daya dan Jasa' => 7,
        'Pemeliharaan Sarana dan Prasarana Sekolah' => 8,
        'Penyediaan Alat Multimedia Pembelajaran' => 9,
        'Peningkatan Kompetensi Keahlian' => 10,
        'Mendukung Keterserapan Lulusan' => 11,
        'Pembayaran Honor' => 12,
    ];

    /**
     * Fitur numerik yang akan dinormalisasi.
     */
    private array $numericFeatures = [
        'x4_jumlah_anggaran',
        'x5_pengeluaran',
        'x6_selisih',
        'x7_persentase_realisasi',
    ];

    /**
     * Penyimpanan parameter normalisasi (min, max per fitur).
     */
    private array $normParams = [];

    /**
     * Penyimpanan label encoding (kode_rekening → integer).
     */
    private array $labelEncoding = [];

    /**
     * Proses data training: join RKAS+BKU, hitung fitur X1-X8, simpan ke tabel data_trainings.
     */
    public function processTrainingData(string $tahun): Collection
    {
        // Ambil data training RKAS & BKU
        $rkasData = DataTrainingRkas::where('tahun', $tahun)->get();
        $bkuData = DataTrainingBku::where('tahun', $tahun)->get();

        if ($rkasData->isEmpty() || $bkuData->isEmpty()) {
            return collect();
        }

        // Group RKAS berdasarkan kode_rekening untuk mempermudah pencocokan berganda
        $rkasGrouped = $rkasData->groupBy('kode_rekening');

        // Ambil semua kode_rekening unik dari RKAS untuk menentukan X8
        $rkasKodeRekening = $rkasData->pluck('kode_rekening')->unique()->toArray();

        // Hapus data training olahan lama untuk tahun ini agar tidak duplikat saat impor ulang
        DataTraining::where('tahun', $tahun)->delete();

        $results = collect();

        foreach ($bkuData as $bku) {
            $candidates = $rkasGrouped->get($bku->kode_rekening);
            $rkas = null;

            if ($candidates && $candidates->isNotEmpty()) {
                if ($candidates->count() === 1) {
                    $rkas = $candidates->first();
                } else {
                    // Cocokkan dengan RKAS yang nilai anggarannya paling mendekati nilai pengeluaran BKU
                    $pengeluaran = (float) ($bku->pengeluaran ?? 0);
                    $rkas = $candidates->sortBy(fn($c) => abs((float)($c->jumlah_setelah ?? 0) - $pengeluaran))->first();
                }
            }

            // Hitung fitur X1 - X8
            $features = $this->calculateFeatures($bku, $rkas, $rkasKodeRekening);

            // Simpan sebagai record baru (menggunakan create agar transaksi bernilai sama tetap tersimpan)
            $training = DataTraining::create(
                array_merge($features, [
                    'tahun' => $tahun,
                    'kode_rekening' => $bku->kode_rekening,
                    'label' => $bku->label_aktual,
                ])
            );

            $results->push($training);
        }

        return $results;
    }

    /**
     * Hitung fitur X1-X8 dari data BKU dan RKAS.
     *
     * X1: Kode Rekening (kategorikal, join key)
     * X2: Standar Komponen S Setelah Perubahan (dari RKAS)
     * X3: Standar Komponen K Setelah Perubahan (dari RKAS)
     * X4: Jumlah Anggaran Setelah Perubahan (dari RKAS)
     * X5: Pengeluaran (dari BKU)
     * X6: Selisih Realisasi vs Anggaran (Pengeluaran - Jumlah Setelah)
     * X7: Persentase Realisasi ((Pengeluaran / Jumlah Setelah) × 100%)
     * X8: Status Kesesuaian Kode Rekening (1 jika kode_rekening BKU ada di RKAS, 0 jika tidak)
     */
    public function calculateFeatures($bku, $rkas, array $rkasKodeRekening): array
    {
        $pengeluaran = (float) ($bku->pengeluaran ?? 0);
        $jumlahAnggaran = $rkas ? (float) ($rkas->jumlah_setelah ?? 0) : 0;
        
        $standarSVal = $rkas?->standar_komponen_s_setelah;
        $standarKVal = $rkas?->standar_komponen_k_setelah;
        
        $standarS = is_numeric($standarSVal) ? (float) $standarSVal : $this->mapStandardToNumeric($standarSVal ?? '');
        $standarK = is_numeric($standarKVal) ? (float) $standarKVal : $this->mapActivityToNumeric($standarKVal ?? '');

        // X6: Selisih Realisasi vs Anggaran
        $selisih = $pengeluaran - $jumlahAnggaran;

        // X7: Persentase Realisasi
        $persentaseRealisasi = $jumlahAnggaran > 0
            ? ($pengeluaran / $jumlahAnggaran) * 100
            : 0;

        // X8: Status Kesesuaian Kode Rekening
        $statusKesesuaian = in_array($bku->kode_rekening, $rkasKodeRekening) ? 1 : 0;

        return [
            'x1_kode_rekening' => $bku->kode_rekening,
            'x2_standar_komponen_s' => $standarS,
            'x3_standar_komponen_k' => $standarK,
            'x4_jumlah_anggaran' => $jumlahAnggaran,
            'x5_pengeluaran' => $pengeluaran,
            'x6_selisih' => $selisih,
            'x7_persentase_realisasi' => round($persentaseRealisasi, 2),
            'x8_status_kesesuaian' => $statusKesesuaian,
        ];
    }

    /**
     * Lakukan label encoding pada fitur kategorikal (X1: kode_rekening).
     * Mengubah kode_rekening string menjadi integer.
     */
    public function labelEncode(Collection $data): Collection
    {
        // Bangun mapping jika belum ada
        if (empty($this->labelEncoding)) {
            // Gunakan encoding tetap dari user terlebih dahulu
            $this->labelEncoding = self::ENCODING_KODE_REKENING;
            
            // Tambahkan kode rekening lain yang belum terdaftar secara dinamis
            $uniqueKodes = $data->pluck('x1_kode_rekening')->unique()->values();
            $nextIndex = count($this->labelEncoding) + 1;
            foreach ($uniqueKodes as $kode) {
                if (!isset($this->labelEncoding[$kode])) {
                    $this->labelEncoding[$kode] = $nextIndex++;
                }
            }
        }

        return $data->map(function ($item) {
            $encoded = is_array($item) ? $item : $item->toArray();
            $encoded['x1_encoded'] = $this->labelEncoding[$encoded['x1_kode_rekening']] ?? 0;
            return $encoded;
        });
    }

    /**
     * Normalisasi min-max untuk fitur numerik.
     * x_norm = (x - min) / (max - min)
     *
     * @param Collection $data Data dengan fitur numerik
     * @param bool $fit Jika true, hitung parameter normalisasi dari data ini (training). Jika false, gunakan parameter yang sudah ada (prediksi).
     */
    public function normalizeMinMax(Collection $data, bool $fit = true): Collection
    {
        if ($fit) {
            // Hitung min dan max per fitur dari data training
            foreach ($this->numericFeatures as $feature) {
                $values = $data->pluck($feature)->filter(fn($v) => $v !== null)->map(fn($v) => (float) $v);
                $this->normParams[$feature] = [
                    'min' => $values->min() ?? 0,
                    'max' => $values->max() ?? 0,
                ];
            }
        }

        return $data->map(function ($item) {
            $normalized = is_array($item) ? $item : $item->toArray();
            foreach ($this->numericFeatures as $feature) {
                $min = $this->normParams[$feature]['min'] ?? 0;
                $max = $this->normParams[$feature]['max'] ?? 0;
                $range = $max - $min;
                $value = (float) ($normalized[$feature] ?? 0);
                $normalized[$feature . '_norm'] = $range > 0 ? ($value - $min) / $range : 0;
            }
            return $normalized;
        });
    }

    /**
     * Pipeline preprocessing lengkap.
     */
    public function preprocess(Collection $data, bool $fit = true): Collection
    {
        // 1. Label encoding
        $data = $this->labelEncode($data);

        // 2. Normalisasi min-max
        $data = $this->normalizeMinMax($data, $fit);

        return $data;
    }

    /**
     * Get parameter normalisasi (untuk disimpan/digunakan saat prediksi).
     */
    public function getNormParams(): array
    {
        return $this->normParams;
    }

    /**
     * Set parameter normalisasi (dari model yang sudah di-train).
     */
    public function setNormParams(array $params): void
    {
        $this->normParams = $params;
    }

    /**
     * Get label encoding mapping.
     */
    public function getLabelEncoding(): array
    {
        return $this->labelEncoding;
    }

    /**
     * Set label encoding mapping.
     */
    public function setLabelEncoding(array $encoding): void
    {
        $this->labelEncoding = $encoding;
    }

    /**
     * Map standard component text description to a numeric value (1 to 8).
     */
    private function mapStandardToNumeric(string $name): float
    {
        if (empty($name)) return 0.0;
        
        $nameTrimmed = trim($name);
        
        // 1. Cek kecocokan persis dengan tabel encoding
        if (isset(self::ENCODING_KOMPONEN_S[$nameTrimmed])) {
            return (float) self::ENCODING_KOMPONEN_S[$nameTrimmed];
        }
        
        // 2. Cek kecocokan keyword (kasus penulisan berbeda)
        foreach (self::ENCODING_KOMPONEN_S as $key => $val) {
            if (stripos($nameTrimmed, $key) !== false || stripos($key, $nameTrimmed) !== false) {
                return (float) $val;
            }
        }
        
        // 3. Fallback ke SNP keywords
        if (stripos($nameTrimmed, 'Isi') !== false) return 1.0;
        if (stripos($nameTrimmed, 'Proses') !== false) return 2.0;
        if (stripos($nameTrimmed, 'Lulusan') !== false || stripos($nameTrimmed, 'Kompetensi') !== false) return 3.0;
        if (stripos($nameTrimmed, 'Pendidik') !== false || stripos($nameTrimmed, 'Kependidikan') !== false || stripos($nameTrimmed, 'Guru') !== false || stripos($nameTrimmed, 'Tendik') !== false) return 4.0;
        if (stripos($nameTrimmed, 'Sarana') !== false || stripos($nameTrimmed, 'Prasarana') !== false) return 5.0;
        if (stripos($nameTrimmed, 'Pengelolaan') !== false || stripos($nameTrimmed, 'Manajemen') !== false) return 6.0;
        if (stripos($nameTrimmed, 'Pembiayaan') !== false) return 7.0;
        if (stripos($nameTrimmed, 'Penilaian') !== false || stripos($nameTrimmed, 'Evaluasi') !== false) return 8.0;
        
        // Fallback akhir ke hash (crc32) dalam rentang 1-8
        return (float) ((abs(crc32($nameTrimmed)) % 8) + 1);
    }

    /**
     * Map activity description to a numeric value (1 to 12).
     */
    private function mapActivityToNumeric(string $name): float
    {
        if (empty($name)) return 0.0;
        
        $nameTrimmed = trim($name);
        
        // 1. Cek kecocokan persis dengan tabel encoding
        if (isset(self::ENCODING_KOMPONEN_K[$nameTrimmed])) {
            return (float) self::ENCODING_KOMPONEN_K[$nameTrimmed];
        }
        
        // 2. Cek kecocokan keyword (kasus penulisan berbeda)
        foreach (self::ENCODING_KOMPONEN_K as $key => $val) {
            if (stripos($nameTrimmed, $key) !== false || stripos($key, $nameTrimmed) !== false) {
                return (float) $val;
            }
        }
        
        // 3. Fallback ke Permendikbud keywords
        if (stripos($nameTrimmed, 'PPDB') !== false || stripos($nameTrimmed, 'Peserta Didik Baru') !== false) return 1.0;
        if (stripos($nameTrimmed, 'Perpustakaan') !== false || stripos($nameTrimmed, 'Buku') !== false) return 2.0;
        if (stripos($nameTrimmed, 'Ekstrakurikuler') !== false || (stripos($nameTrimmed, 'Pembelajaran') !== false && stripos($nameTrimmed, 'Asesmen') === false && stripos($nameTrimmed, 'Evaluasi') === false)) return 3.0;
        if (stripos($nameTrimmed, 'Asesmen') !== false || stripos($nameTrimmed, 'Evaluasi') !== false || stripos($nameTrimmed, 'Ujian') !== false) return 4.0;
        if (stripos($nameTrimmed, 'Administrasi') !== false || stripos($nameTrimmed, 'ATK') !== false || stripos($nameTrimmed, 'Cetak') !== false) return 5.0;
        if (stripos($nameTrimmed, 'Profesi') !== false || (stripos($nameTrimmed, 'Guru') !== false && stripos($nameTrimmed, 'Honor') === false)) return 6.0;
        if (stripos($nameTrimmed, 'Daya') !== false || stripos($nameTrimmed, 'Jasa') !== false || stripos($nameTrimmed, 'Listrik') !== false || stripos($nameTrimmed, 'Internet') !== false) return 7.0;
        if (stripos($nameTrimmed, 'Pemeliharaan') !== false || stripos($nameTrimmed, 'Gedung') !== false || stripos($nameTrimmed, 'Rehab') !== false) return 8.0;
        if (stripos($nameTrimmed, 'Multimedia') !== false || stripos($nameTrimmed, 'Komputer') !== false || stripos($nameTrimmed, 'Laptop') !== false) return 9.0;
        if (stripos($nameTrimmed, 'Keahlian') !== false || stripos($nameTrimmed, 'UKK') !== false) return 10.0;
        if (stripos($nameTrimmed, 'Keterserapan') !== false || stripos($nameTrimmed, 'Bursa Kerja') !== false || stripos($nameTrimmed, 'BKK') !== false) return 11.0;
        if (stripos($nameTrimmed, 'Honor') !== false || stripos($nameTrimmed, 'Honorarium') !== false || stripos($nameTrimmed, 'Gaji') !== false) return 12.0;
        
        // Fallback akhir ke hash (crc32) dalam rentang 1-12
        return (float) ((abs(crc32($nameTrimmed)) % 12) + 1);
    }
}
