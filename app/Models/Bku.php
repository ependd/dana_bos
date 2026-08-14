<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bku extends Model
{
    use HasFactory;

    protected $fillable = [
        'rkas_id',
        'periode_bos_id',
        'kode_bku',
        'status',
        'total_realisasi',
    ];

    protected function casts(): array
    {
        return [
            'total_realisasi' => 'decimal:2',
        ];
    }

    // === Status Constants ===

    const STATUS_DRAFT = 'draft';
    const STATUS_AKTIF = 'aktif';
    const STATUS_SELESAI = 'selesai';

    // === Relationships ===

    public function rkas(): BelongsTo
    {
        return $this->belongsTo(Rkas::class);
    }

    public function periodeBos(): BelongsTo
    {
        return $this->belongsTo(PeriodeBos::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BkuItem::class);
    }

    // === Helpers ===

    public function hitungTotalRealisasi(): void
    {
        $this->total_realisasi = $this->items()->sum('pengeluaran');
        $this->save();
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_AKTIF => 'info',
            self::STATUS_SELESAI => 'success',
            default => 'gray',
        };
    }

    public function getTotalPenerimaanAttribute(): float
    {
        return (float) ($this->periodeBos?->anggaran ?? 0);
    }

    public function getTotalPengeluaranAttribute(): float
    {
        return (float) ($this->total_realisasi ?? 0);
    }

    public function getSaldoAkhirAttribute(): float
    {
        return $this->total_penerimaan - $this->total_pengeluaran;
    }
}
