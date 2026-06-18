<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['pesanan_id', 'produk_id', 'nama_produk', 'harga_saat_itu', 'qty', 'subtotal', 'catatan_item'])]
class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';

    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'harga_saat_itu' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
