<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WhitelistedIp;

class WhitelistedIpPolicy
{
    /**
     * Only super admins may manage the IP whitelist.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, WhitelistedIp $whitelistedIp): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, WhitelistedIp $whitelistedIp): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, WhitelistedIp $whitelistedIp): bool
    {
        return $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function restore(User $user, WhitelistedIp $whitelistedIp): bool
    {
        return $user->isSuperAdmin();
    }

    public function forceDelete(User $user, WhitelistedIp $whitelistedIp): bool
    {
        return $user->isSuperAdmin();
    }
}
