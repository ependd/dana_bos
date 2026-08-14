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

    public function __construct(string $tahun)
    {
        $this->tahun = $tahun;
    }

    public function model(array $row): ?DataTrainingRkas
    {
        // Skip baris kosong
        if (empty($row['kode_rekening'])) {
            return null;
        }

        return new DataTrainingRkas([
            'tahun' => $this->tahun,
            'kode_rekening' => $row['kode_rekening'],
            'uraian_belanja' => $row['uraian_belanja'] ?? null,
            'volume_sebelum' => $row['volume_sebelum'] ?? null,
            'satuan_sebelum' => $row['satuan_sebelum'] ?? null,
            'harga_satuan_sebelum' => $row['harga_satuan_sebelum'] ?? null,
            'jumlah_sebelum' => $row['jumlah_sebelum'] ?? null,
            'standar_komponen_s_sebelum' => $row['standar_komponen_s_sebelum'] ?? null,
            'standar_komponen_k_sebelum' => $row['standar_komponen_k_sebelum'] ?? null,
            'volume_setelah' => $row['volume_setelah'] ?? null,
            'satuan_setelah' => $row['satuan_setelah'] ?? null,
            'harga_satuan_setelah' => $row['harga_satuan_setelah'] ?? null,
            'jumlah_setelah' => $row['jumlah_setelah'] ?? null,
            'standar_komponen_s_setelah' => $row['standar_komponen_s_setelah'] ?? null,
            'standar_komponen_k_setelah' => $row['standar_komponen_k_setelah'] ?? null,
            'bertambah_berkurang' => $row['bertambah_berkurang'] ?? null,
            'persentase_perubahan' => $row['persentase_perubahan'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'kode_rekening' => 'required|string',
        ];
    }
}
