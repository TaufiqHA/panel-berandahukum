<?php

namespace App\Http\Middleware;

use App\Models\IpWhitelistSetting;
use App\Models\User;
use App\Models\WhitelistedIp;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class CheckWhitelistedIp
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->isWhitelistEnabled()) {
            return $next($request);
        }

        if ($this->isAllowed($request)) {
            return $next($request);
        }

        abort(403, 'Akses ditolak. IP Anda tidak terdaftar pada whitelist.');
    }

    /**
     * Whether the whitelist is active. Defaults to inactive so the app stays
     * reachable before the migrations have been run.
     */
    protected function isWhitelistEnabled(): bool
    {
        if (! Schema::hasTable('ip_whitelist_settings')) {
            return false;
        }

        return IpWhitelistSetting::isEnabled();
    }

    /**
     * Whether the current request may bypass the whitelist.
     */
    protected function isAllowed(Request $request): bool
    {
        if (WhitelistedIp::allows($request->ip())) {
            return true;
        }

        $user = $request->user();

        // Superadmin always has access so they can manage the whitelist and
        // never lock themselves out.
        if ($user instanceof User && $user->isSuperAdmin()) {
            return true;
        }

        // Let guests reach the authentication flow so a superadmin can log in
        // from an address that is not whitelisted yet.
        if ($request->routeIs('filament.admin.auth.*')) {
            return true;
        }

        // The login form is submitted through Livewire by unauthenticated
        // guests, so their requests must be allowed through.
        return $request->user() === null && $request->is('livewire/*');
    }
}
