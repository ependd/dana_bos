<?php

namespace App\Imports;

use App\Models\DataTrainingBku;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class DataTrainingBkuImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    private string $tahun;

    public function __construct(string $tahun)
    {
        $this->tahun = $tahun;
    }

    public function model(array $row): ?DataTrainingBku
    {
        // Skip baris kosong
        if (empty($row['kode_rekening'])) {
            return null;
        }

        return new DataTrainingBku([
            'tahun' => $this->tahun,
            'nomor_urut' => $row['nomor_urut'] ?? null,
            'tanggal' => $row['tanggal'] ?? null,
            'nomor_bukti' => $row['nomor_bukti'] ?? null,
            'uraian' => $row['uraian'] ?? null,
            'nilai_rincian' => $row['nilai_rincian'] ?? null,
            'kode_rekening' => $row['kode_rekening'],
            'penerimaan' => $row['penerimaan'] ?? 0,
            'pengeluaran' => $row['pengeluaran'] ?? 0,
            'saldo' => $row['saldo'] ?? 0,
            'label_aktual' => $row['label_aktual'] ?? 'Wajar',
        ]);
    }

    public function rules(): array
    {
        return [
            'kode_rekening' => 'required|string',
            'label_aktual' => 'required|in:Wajar,Menyimpang',
        ];
    }
}
