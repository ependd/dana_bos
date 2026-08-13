<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Kebutuhan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'periode_bos_id',
        'katalog_barang_id',
        'nama_barang',
        'harga_satuan',
        'rkas_item_id',
        'kategori_belanja',
        'volume',
        'satuan',
        'keterangan',
        'status',
        'catatan_admin',
        'tanggal_konfirmasi',
    ];

    protected function casts(): array
    {
        return [
            'harga_satuan' => 'decimal:2',
            'tanggal_konfirmasi' => 'datetime',
        ];
    }

    // === Status Constants ===

    const STATUS_DIAJUKAN = 'diajukan';
    const STATUS_DITERIMA = 'diterima';
    const STATUS_DITOLAK = 'ditolak';
    const STATUS_TERSEDIA = 'tersedia';
    const STATUS_DIKONFIRMASI = 'dikonfirmasi';

    // === Scopes ===

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // === Relationships ===

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function periodeBos(): BelongsTo
    {
        return $this->belongsTo(PeriodeBos::class);
    }

    public function katalogBarang(): BelongsTo
    {
        return $this->belongsTo(KatalogBarang::class);
    }

    public function getNamaBarangAttribute($value): ?string
    {
        return $value ?? $this->katalogBarang?->nama_barang;
    }

    public function rkasItem(): BelongsTo
    {
        return $this->belongsTo(RkasItem::class);
    }

    // === Helpers ===

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DIAJUKAN => 'warning',
            self::STATUS_DITERIMA, 'disetujui' => 'info',
            self::STATUS_DITOLAK => 'danger',
            self::STATUS_TERSEDIA => 'purple',
            self::STATUS_DIKONFIRMASI => 'success',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DIAJUKAN => 'Diajukan',
            self::STATUS_DITERIMA, 'disetujui' => 'Disetujui',
            self::STATUS_DITOLAK => 'Ditolak',
            self::STATUS_TERSEDIA => 'Barang Tersedia',
            self::STATUS_DIKONFIRMASI => 'Dikonfirmasi',
            default => ucfirst($this->status),
        };
    }

    public function getTotalHargaEstimasiAttribute(): float
    {
        $harga = $this->harga_satuan ?? $this->katalogBarang?->harga_referensi ?? 0;
        return (float) ($this->volume * $harga);
    }
}
