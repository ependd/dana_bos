<?php

namespace App\Imports;

use App\Models\DataTrainingRkas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class DataTrainingRkasImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    private string $tahun;

    public function __construct(string $tahun = '2024')
    {
        $this->tahun = $tahun;
    }

    public function model(array $row): ?DataTrainingRkas
    {
        // Skip baris kosong
        if (empty($row['kode_rekening'])) {
            return null;
        }

        $cleanNumber = function ($val) {
            if ($val === null || $val === '') return null;
            if (is_numeric($val)) return (float) $val;
            $val = str_ireplace(['rp', ' '], '', (string)$val);
            if (strpos($val, ',') !== false && strpos($val, '.') !== false) {
                $val = str_replace('.', '', $val);
                $val = str_replace(',', '.', $val);
            } elseif (strpos($val, '.') !== false && substr_count($val, '.') > 1) {
                $val = str_replace('.', '', $val);
            } elseif (strpos($val, ',') !== false) {
                $val = str_replace(',', '.', $val);
            }
            $clean = preg_replace('/[^0-9.-]/', '', $val);
            return $clean !== '' ? (float) $clean : null;
        };

        return new DataTrainingRkas([
            'tahun' => !empty($row['tahun']) ? (string)$row['tahun'] : $this->tahun,
            'kode_rekening' => trim((string)$row['kode_rekening']),
            'uraian_belanja' => $row['uraian_belanja'] ?? null,
            'volume_sebelum' => $cleanNumber($row['volume_sebelum'] ?? null),
            'satuan_sebelum' => $row['satuan_sebelum'] ?? null,
            'harga_satuan_sebelum' => $cleanNumber($row['harga_satuan_sebelum'] ?? null),
            'jumlah_sebelum' => $cleanNumber($row['jumlah_sebelum'] ?? null),
            'standar_komponen_s_sebelum' => $row['standar_komponen_s_sebelum'] ?? null,
            'standar_komponen_k_sebelum' => $row['standar_komponen_k_sebelum'] ?? null,
            'volume_setelah' => $cleanNumber($row['volume_setelah'] ?? null),
            'satuan_setelah' => $row['satuan_setelah'] ?? null,
            'harga_satuan_setelah' => $cleanNumber($row['harga_satuan_setelah'] ?? null),
            'jumlah_setelah' => $cleanNumber($row['jumlah_setelah'] ?? null),
            'standar_komponen_s_setelah' => $row['standar_komponen_s_setelah'] ?? null,
            'standar_komponen_k_setelah' => $row['standar_komponen_k_setelah'] ?? null,
            'bertambah_berkurang' => $cleanNumber($row['bertambah_berkurang'] ?? null),
            'persentase_perubahan' => $cleanNumber($row['persentase_perubahan'] ?? null),
        ]);
    }

    public function rules(): array
    {
        return [
            'kode_rekening' => 'required',
        ];
    }
}
