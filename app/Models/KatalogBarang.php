<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KatalogBarang extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori',
        'satuan',
        'harga_referensi',
        'deskripsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'harga_referensi' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // === Scopes ===

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // === Relationships ===

    public function kebutuhans(): HasMany
    {
        return $this->hasMany(Kebutuhan::class);
    }
}
