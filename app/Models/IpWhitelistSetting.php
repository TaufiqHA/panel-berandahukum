<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpWhitelistSetting extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'enabled',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }

    /**
     * The single global whitelist settings row.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([], ['enabled' => false]);
    }

    /**
     * Whether the IP whitelist is currently enforced.
     */
    public static function isEnabled(): bool
    {
        return (bool) static::query()->value('enabled');
    }

    /**
     * Enable or disable the IP whitelist.
     */
    public static function setEnabled(bool $enabled): self
    {
        $setting = static::current();
        $setting->enabled = $enabled;
        $setting->save();

        return $setting;
    }
}
