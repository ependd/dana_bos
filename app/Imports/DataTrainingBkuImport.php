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

    public function __construct(string $tahun = '2024')
    {
        $this->tahun = $tahun;
    }

    public function model(array $row): ?DataTrainingBku
    {
        // Skip baris kosong
        if (empty($row['kode_rekening'])) {
            return null;
        }

        // Format tanggal jika ada
        $tanggal = null;
        if (!empty($row['tanggal'])) {
            try {
                if (is_numeric($row['tanggal'])) {
                    $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal'])->format('Y-m-d');
                } else {
                    $tanggal = \Carbon\Carbon::parse($row['tanggal'])->format('Y-m-d');
                }
            } catch (\Throwable $e) {
                $tanggal = null;
            }
        }

        // Label aktual normalisasi (Wajar vs Tidak Sesuai)
        $rawLabel = trim((string)($row['label_aktual'] ?? 'Wajar'));
        $label = (strcasecmp($rawLabel, 'Menyimpang') === 0 || strcasecmp($rawLabel, 'Tidak Sesuai') === 0) ? 'Tidak Sesuai' : 'Wajar';

        $cleanNumber = function ($val) {
            if ($val === null || $val === '') return 0;
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
            return (float) preg_replace('/[^0-9.-]/', '', $val);
        };

        return new DataTrainingBku([
            'tahun' => !empty($row['tahun']) ? (string)$row['tahun'] : $this->tahun,
            'nomor_urut' => $row['nomor_urut'] ?? null,
            'tanggal' => $tanggal,
            'nomor_bukti' => $row['nomor_bukti'] ?? null,
            'uraian' => $row['uraian'] ?? null,
            'nilai_rincian' => $cleanNumber($row['nilai_rincian'] ?? 0),
            'kode_rekening' => trim((string)$row['kode_rekening']),
            'penerimaan' => $cleanNumber($row['penerimaan'] ?? 0),
            'pengeluaran' => $cleanNumber($row['pengeluaran'] ?? 0),
            'saldo' => $cleanNumber($row['saldo'] ?? 0),
            'label_aktual' => $label,
        ]);
    }

    public function rules(): array
    {
        return [
            'kode_rekening' => 'required',
        ];
    }
}
