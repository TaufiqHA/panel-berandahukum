<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'status_admin',
        'toko_id',
        'user_menu',
        'signature',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Determine whether the user is a super admin, the only role allowed to
     * manage the IP whitelist and to bypass it.
     */
    public function isSuperAdmin(): bool
    {
        return (int) $this->status === 1 && (int) $this->status_admin !== 1;
    }

    /**
     * Determine whether the user may access the given menu group.
     */
    public function hasMenu(string $menu): bool
    {
        if ($this->status == 1) {
            return true;
        }

        return in_array($menu, explode(',', (string) $this->user_menu), true);
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
