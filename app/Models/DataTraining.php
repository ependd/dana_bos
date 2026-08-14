<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataTraining extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'kode_rekening',
        'x1_kode_rekening',
        'x2_standar_komponen_s',
        'x3_standar_komponen_k',
        'x4_jumlah_anggaran',
        'x5_pengeluaran',
        'x6_selisih',
        'x7_persentase_realisasi',
        'x8_status_kesesuaian',
        'label',
    ];

    protected function casts(): array
    {
        return [
            'x2_standar_komponen_s' => 'float',
            'x3_standar_komponen_k' => 'float',
            'x4_jumlah_anggaran' => 'float',
            'x5_pengeluaran' => 'float',
            'x6_selisih' => 'float',
            'x7_persentase_realisasi' => 'float',
            'x8_status_kesesuaian' => 'integer',
        ];
    }

    /**
     * Fitur terpilih hasil WOA (X1, X2, X3, X4, X7, X8).
     * X5 dan X6 dieliminasi oleh WOA.
     */
    public function getSelectedFeatures(): array
    {
        return [
            'x1_kode_rekening' => $this->x1_kode_rekening,
            'x2_standar_komponen_s' => $this->x2_standar_komponen_s,
            'x3_standar_komponen_k' => $this->x3_standar_komponen_k,
            'x4_jumlah_anggaran' => $this->x4_jumlah_anggaran,
            'x7_persentase_realisasi' => $this->x7_persentase_realisasi,
            'x8_status_kesesuaian' => $this->x8_status_kesesuaian,
        ];
    }

    /**
     * Semua 8 fitur (termasuk yang dieliminasi WOA).
     */
    public function getAllFeatures(): array
    {
        return [
            'x1_kode_rekening' => $this->x1_kode_rekening,
            'x2_standar_komponen_s' => $this->x2_standar_komponen_s,
            'x3_standar_komponen_k' => $this->x3_standar_komponen_k,
            'x4_jumlah_anggaran' => $this->x4_jumlah_anggaran,
            'x5_pengeluaran' => $this->x5_pengeluaran,
            'x6_selisih' => $this->x6_selisih,
            'x7_persentase_realisasi' => $this->x7_persentase_realisasi,
            'x8_status_kesesuaian' => $this->x8_status_kesesuaian,
        ];
    }
}
