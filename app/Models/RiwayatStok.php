<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['bahan_id', 'user_id', 'jenis', 'jumlah', 'keterangan'])]
class RiwayatStok extends Model
{
    protected $table = 'riwayat_stok';

    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
        ];
    }

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(StokBahan::class, 'bahan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
