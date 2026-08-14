<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataTrainingBku extends Model
{
    use HasFactory;

    protected $table = 'data_training_bku';

    protected $fillable = [
        'tahun',
        'nomor_urut',
        'tanggal',
        'nomor_bukti',
        'uraian',
        'nilai_rincian',
        'kode_rekening',
        'penerimaan',
        'pengeluaran',
        'saldo',
        'label_aktual',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'nilai_rincian' => 'decimal:2',
            'penerimaan' => 'decimal:2',
            'pengeluaran' => 'decimal:2',
            'saldo' => 'decimal:2',
        ];
    }
}
