<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataTrainingRkas extends Model
{
    use HasFactory;

    protected $table = 'data_training_rkas';

    protected $fillable = [
        'tahun',
        'kode_rekening',
        'uraian_belanja',
        'volume_sebelum',
        'satuan_sebelum',
        'harga_satuan_sebelum',
        'jumlah_sebelum',
        'standar_komponen_s_sebelum',
        'standar_komponen_k_sebelum',
        'volume_setelah',
        'satuan_setelah',
        'harga_satuan_setelah',
        'jumlah_setelah',
        'standar_komponen_s_setelah',
        'standar_komponen_k_setelah',
        'bertambah_berkurang',
        'persentase_perubahan',
    ];

    protected function casts(): array
    {
        return [
            'volume_sebelum' => 'decimal:2',
            'harga_satuan_sebelum' => 'decimal:2',
            'jumlah_sebelum' => 'decimal:2',
            'standar_komponen_s_sebelum' => 'string',
            'standar_komponen_k_sebelum' => 'string',
            'volume_setelah' => 'decimal:2',
            'harga_satuan_setelah' => 'decimal:2',
            'jumlah_setelah' => 'decimal:2',
            'standar_komponen_s_setelah' => 'string',
            'standar_komponen_k_setelah' => 'string',
            'bertambah_berkurang' => 'decimal:2',
            'persentase_perubahan' => 'decimal:2',
        ];
    }
}
