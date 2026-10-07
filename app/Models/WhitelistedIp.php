<?php

namespace App\Models;

use Database\Factories\WhitelistedIpFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhitelistedIp extends Model
{
    /** @use HasFactory<WhitelistedIpFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'ip_address',
        'keterangan',
    ];

    /**
     * Determine whether the given IP address is whitelisted.
     */
    public static function allows(?string $ipAddress): bool
    {
        if (blank($ipAddress)) {
            return false;
        }

        return static::query()->where('ip_address', $ipAddress)->exists();
    }
}
