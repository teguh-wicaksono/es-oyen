<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'kategori', 'jumlah', 'satuan', 'stok_minimum', 'keterangan'])]
class StokBahan extends Model
{
    protected $table = 'stok_bahan';

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'stok_minimum' => 'decimal:2',
        ];
    }

    public function riwayat(): HasMany
    {
        return $this->hasMany(RiwayatStok::class, 'bahan_id');
    }
}
