<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password', 'role', 'avatar', 'no_hp', 'alamat'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'kasir_id');
    }

    public function chatTerkirim(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'from_user_id');
    }

    public function chatDiterima(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'to_user_id');
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }
}
