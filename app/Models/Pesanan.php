<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kode_pesanan', 'kasir_id', 'nama_kasir', 'nama_pelanggan', 'no_meja', 'catatan', 'status', 'total_harga', 'dibayar', 'kembalian'])]
class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected function casts(): array
    {
        return [
            'total_harga' => 'decimal:2',
            'dibayar' => 'decimal:2',
            'kembalian' => 'decimal:2',
        ];
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_id');
    }
}
