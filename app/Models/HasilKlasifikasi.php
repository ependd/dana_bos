<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilKlasifikasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'periode_bos_id',
        'metode',
        'bku_item_id',
        'rkas_item_id',
        'kode_rekening',
        'x1_kode_rekening',
        'x2_standar_komponen_s',
        'x3_standar_komponen_k',
        'x4_jumlah_anggaran',
        'x5_pengeluaran',
        'x6_selisih',
        'x7_persentase_realisasi',
        'x8_status_kesesuaian',
        'label_prediksi',
        'probabilitas_wajar',
        'probabilitas_menyimpang',
        'classified_at',
        'classified_by',
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
            'probabilitas_wajar' => 'float',
            'probabilitas_menyimpang' => 'float',
            'classified_at' => 'datetime',
        ];
    }

    // === Relationships ===

    public function periodeBos(): BelongsTo
    {
        return $this->belongsTo(PeriodeBos::class);
    }

    public function bkuItem(): BelongsTo
    {
        return $this->belongsTo(BkuItem::class);
    }

    public function rkasItem(): BelongsTo
    {
        return $this->belongsTo(RkasItem::class);
    }

    public function classifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'classified_by');
    }

    // === Helpers ===

    public function isWajar(): bool
    {
        return $this->label_prediksi === 'Wajar';
    }

    public function isMenyimpang(): bool
    {
        return $this->label_prediksi === 'Menyimpang' || $this->label_prediksi === 'Tidak Sesuai';
    }

    public function isTidakSesuai(): bool
    {
        return $this->isMenyimpang();
    }

    public function getLabelBadgeColorAttribute(): string
    {
        return $this->isWajar() ? 'success' : 'danger';
    }

    public function getKeteranganAttribute(): string
    {
        if ($this->isWajar()) {
            return '-';
        }

        $reasons = [];

        if ($this->x8_status_kesesuaian == 0) {
            $reasons[] = "Kode rekening tidak terdaftar dalam RKAS";
        }

        if ($this->x4_jumlah_anggaran == 0 && $this->x5_pengeluaran > 0) {
            $reasons[] = "Pengeluaran dilakukan tanpa pagu anggaran";
        } elseif ($this->x6_selisih > 0 || $this->x7_persentase_realisasi > 100) {
            $reasons[] = "Pengeluaran melebihi pagu anggaran";
        }

        if (empty($reasons)) {
            $reasons[] = "Pengeluaran tidak sesuai dengan ketentuan RKAS";
        }

        return implode('; ', $reasons);
    }

    public function getX1EncodedAttribute(): int
    {
        $mapping = [
            '5.2.1.01.06' => 1,
            '5.2.1.04.02' => 2,
            '5.2.2.01.01' => 3,
            '5.2.2.01.05' => 4,
            '5.2.2.06.01' => 5,
            '5.2.2.11.02' => 6,
            '5.2.3.28.14' => 7,
            '5.2.3.28.27' => 8,
            '5.2.3.11.08' => 9,
            '5.2.3.12.18' => 10,
            'ATK' => 11,
        ];
        return $mapping[$this->kode_rekening] ?? 11;
    }
}
