<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'judul', 'pesan', 'tipe', 'dibaca', 'url'])]
class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'dibaca' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
