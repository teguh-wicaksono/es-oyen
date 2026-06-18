<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama'])]
class Kategori extends Model
{
    protected $table = 'kategori';

    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class, 'kategori_id');
    }
}
