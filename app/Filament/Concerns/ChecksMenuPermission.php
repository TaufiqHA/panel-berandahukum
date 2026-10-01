<?php

namespace App\Filament\Concerns;

trait ChecksMenuPermission
{
    /**
     * The legacy `user_menu` key required to access this resource/page.
     * Return null to allow access for every authenticated user.
     */
    protected static function menuPermission(): ?string
    {
        return null;
    }

    /**
     * Whether users with the "Admin Pusat" (status_admin = 1) flag are blocked.
     */
    protected static function blockedForAdminPusat(): bool
    {
        return false;
    }

    protected static function checkMenuPermission(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        $menu = static::menuPermission();

        if ($menu === null) {
            return true;
        }

        if (static::blockedForAdminPusat() && (int) $user->status_admin === 1) {
            return false;
        }

        return $user->hasMenu($menu);
    }

    public static function canViewAny(): bool
    {
        return static::checkMenuPermission();
    }

    public static function canAccess(): bool
    {
        return static::checkMenuPermission();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::checkMenuPermission();
    }
}
