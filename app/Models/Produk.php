<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kategori_id', 'nama', 'deskripsi', 'harga', 'stok', 'gambar', 'aktif'])]
class Produk extends Model
{
    protected $table = 'produk';

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'aktif' => 'boolean',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'produk_id');
    }
}
