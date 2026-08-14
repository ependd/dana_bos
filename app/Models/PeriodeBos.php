<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodeBos extends Model
{
    use HasFactory;

    protected $table = 'periode_bos';

    protected $fillable = [
        'tahun_ajaran',
        'tanggal_mulai',
        'tanggal_selesai',
        'anggaran',
        'status_pengajuan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'anggaran' => 'decimal:2',
            'status_pengajuan' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // === Scopes ===

    public function scopeActive($query)
    {
        $today = now()->toDateString();
        return $query->where('tanggal_mulai', '<=', $today)
                     ->where('tanggal_selesai', '>=', $today);
    }

    public function scopePengajuanDibuka($query)
    {
        return $query->where('status_pengajuan', true);
    }

    // === Relationships ===

    public function kebutuhans(): HasMany
    {
        return $this->hasMany(Kebutuhan::class);
    }

    public function rkas(): HasMany
    {
        return $this->hasMany(Rkas::class);
    }

    public function bkus(): HasMany
    {
        return $this->hasMany(Bku::class);
    }

    public function hasilKlasifikasis(): HasMany
    {
        return $this->hasMany(HasilKlasifikasi::class);
    }

    // === Helpers ===

    public function getNamaLengkapAttribute(): string
    {
        return "Tahun Ajaran {$this->tahun_ajaran}";
    }
}
