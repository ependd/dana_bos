<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rkas extends Model
{
    use HasFactory;

    protected $fillable = [
        'periode_bos_id',
        'created_by',
        'kode_rkas',
        'status',
        'total_anggaran',
        'catatan',
        'catatan_penolakan',
        'revisi_ke',
        'submitted_at',
        'approved_by',
        'approved_at',
        'qr_code_path',
    ];

    protected function casts(): array
    {
        return [
            'total_anggaran' => 'decimal:2',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    // === Status Constants ===

    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REVISED = 'revised';

    // === Scopes ===

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // === Relationships ===

    public function periodeBos(): BelongsTo
    {
        return $this->belongsTo(PeriodeBos::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RkasItem::class)
            ->orderByRaw('CAST(NULLIF(standar_komponen_s_setelah, "") AS UNSIGNED) ASC, CAST(NULLIF(standar_komponen_k_setelah, "") AS UNSIGNED) ASC, kode_rekening ASC, id ASC');
    }

    public function bku(): HasOne
    {
        return $this->hasOne(Bku::class);
    }

    // === Helpers ===

    public function hitungTotalAnggaran(): void
    {
        $this->total_anggaran = $this->items()->sum('jumlah_setelah');
        $this->save();
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_SUBMITTED => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_REVISED => 'info',
            default => 'gray',
        };
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED, self::STATUS_REVISED]);
    }

    /**
     * Hitung breakdown alokasi Pagu Anggaran BOS (Honor, Buku, Sarpras, Operasional).
     */
    public function getPaguBreakdown(): array
    {
        $periode = $this->periodeBos;
        $totalPagu = (float) ($periode?->anggaran ?? 0);
        $totalRkas = (float) $this->total_anggaran;

        $items = $this->items()->with(['kebutuhan.katalogBarang'])->get();

        $honorNominal = 0;
        $bukuNominal = 0;
        $sarprasNominal = 0;
        $operasionalNominal = 0;

        foreach ($items as $item) {
            $jumlah = (float) $item->jumlah_setelah;
            $kategori = strtolower(trim($item->kebutuhan?->kategori_belanja ?? $item->kebutuhan?->katalogBarang?->kategori ?? ''));
            $komponenK = strtolower(trim($item->standar_komponen_k_setelah ?? ''));
            $uraian = strtolower(trim($item->uraian_belanja ?? ''));

            // Match Honorarium
            if ($kategori === 'honorarium' || $kategori === 'honor' || str_contains($komponenK, 'honor') || $komponenK === '12' || str_contains($uraian, 'honor')) {
                $honorNominal += $jumlah;
            }
            // Match Buku & Perpustakaan
            elseif ($kategori === 'bahan ajar' || str_contains($komponenK, 'perpustakaan') || $komponenK === '2' || str_contains($uraian, 'buku')) {
                $bukuNominal += $jumlah;
            }
            // Match Sarana & Prasarana
            elseif (in_array($kategori, ['pemeliharaan', 'mebeler', 'peralatan lab']) || str_contains($komponenK, 'sarana') || str_contains($komponenK, 'pemeliharaan') || $komponenK === '7' || $komponenK === '8') {
                $sarprasNominal += $jumlah;
            }
            // Match Operasional & Kegiatan
            else {
                $operasionalNominal += $jumlah;
            }
        }

        $honorPct = $totalPagu > 0 ? ($honorNominal / $totalPagu) * 100 : 0;
        $bukuPct = $totalPagu > 0 ? ($bukuNominal / $totalPagu) * 100 : 0;
        $sarprasPct = $totalPagu > 0 ? ($sarprasNominal / $totalPagu) * 100 : 0;
        $operasionalPct = $totalPagu > 0 ? ($operasionalNominal / $totalPagu) * 100 : 0;
        $totalRkasPct = $totalPagu > 0 ? ($totalRkas / $totalPagu) * 100 : 0;

        return [
            'total_pagu' => $totalPagu,
            'total_rkas' => $totalRkas,
            'total_rkas_pct' => round($totalRkasPct, 1),
            'total_rkas_status' => $totalPagu > 0 && $totalRkas > $totalPagu ? 'danger' : 'success',

            'honor' => [
                'nominal' => $honorNominal,
                'pct' => round($honorPct, 1),
                'target' => 'Maksimal 20%',
                'status' => $honorPct > 20 ? 'danger' : 'success',
                'message' => $honorPct > 20 ? 'Melebihi Batas Maksimal 20%' : 'Dalam Batas Aman (≤ 20%)',
            ],
            'buku' => [
                'nominal' => $bukuNominal,
                'pct' => round($bukuPct, 1),
                'target' => 'Minimal 20%',
                'status' => $bukuPct < 20 ? 'warning' : 'success',
                'message' => $bukuPct < 20 ? 'Belum Memenuhi Minimal 20%' : 'Memenuhi Syarat Minimal (≥ 20%)',
            ],
            'sarpras' => [
                'nominal' => $sarprasNominal,
                'pct' => round($sarprasPct, 1),
                'target' => 'Maksimal 20%',
                'status' => $sarprasPct > 20 ? 'danger' : 'success',
                'message' => $sarprasPct > 20 ? 'Melebihi Batas Maksimal 20%' : 'Dalam Batas Aman (≤ 20%)',
            ],
            'operasional' => [
                'nominal' => $operasionalNominal,
                'pct' => round($operasionalPct, 1),
                'target' => 'Fleksibel (± 40%)',
                'status' => 'info',
                'message' => 'Alokasi Operasional & Kegiatan Sekolah',
            ],
        ];
    }
}
