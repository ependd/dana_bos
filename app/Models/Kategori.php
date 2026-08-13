<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $fillable = ['nama', 'kelompok'];

    public function katalogBarangs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(KatalogBarang::class, 'kategori', 'nama');
    }
}
