<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BkuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bku_id',
        'rkas_item_id',
        'nomor_urut',
        'tanggal',
        'nomor_bukti',
        'uraian',
        'nilai_rincian',
        'kode_rekening',
        'penerimaan',
        'pengeluaran',
        'saldo',
        'foto_barang',
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

    protected static function booted()
    {
        static::saved(function ($bkuItem) {
            if ($bkuItem->pengeluaran > 0 && $bkuItem->rkas_item_id) {
                app(\App\Services\BkuService::class)->notifyGuru($bkuItem);
            }
        });
    }

    // === Relationships ===

    public function bku(): BelongsTo
    {
        return $this->belongsTo(Bku::class);
    }

    public function rkasItem(): BelongsTo
    {
        return $this->belongsTo(RkasItem::class);
    }

    public function hasilKlasifikasis(): HasMany
    {
        return $this->hasMany(HasilKlasifikasi::class);
    }
}
