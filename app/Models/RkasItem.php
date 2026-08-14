<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RkasItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'rkas_id',
        'kebutuhan_id',
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
        'status_barang_datang',
        'tanggal_barang_datang',
        'catatan_barang_datang',
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
            'tanggal_barang_datang' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (RkasItem $item) {
            $item->hitungJumlahSetelah();
            $item->hitungPerubahan();
        });
    }

    // === Relationships ===

    public function rkas(): BelongsTo
    {
        return $this->belongsTo(Rkas::class);
    }

    public function kebutuhan(): BelongsTo
    {
        return $this->belongsTo(Kebutuhan::class);
    }

    public function kebutuhans(): HasMany
    {
        return $this->hasMany(Kebutuhan::class, 'rkas_item_id');
    }

    public function bkuItems(): HasMany
    {
        return $this->hasMany(BkuItem::class);
    }

    public function hasilKlasifikasis(): HasMany
    {
        return $this->hasMany(HasilKlasifikasi::class);
    }

    // === Helpers ===

    /**
     * Hitung jumlah otomatis dari volume × harga satuan.
     */
    public function hitungJumlahSetelah(): void
    {
        $this->jumlah_setelah = $this->volume_setelah * $this->harga_satuan_setelah;
    }

    /**
     * Hitung perubahan antara sebelum dan setelah.
     */
    public function hitungPerubahan(): void
    {
        if ($this->jumlah_sebelum && $this->jumlah_sebelum > 0) {
            $this->bertambah_berkurang = $this->jumlah_setelah - $this->jumlah_sebelum;
            $this->persentase_perubahan = ($this->bertambah_berkurang / $this->jumlah_sebelum) * 100;
        } else {
            $this->bertambah_berkurang = $this->jumlah_setelah;
            $this->persentase_perubahan = 100;
        }
    }
}
