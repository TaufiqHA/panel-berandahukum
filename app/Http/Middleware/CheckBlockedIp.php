<?php

namespace App\Http\Middleware;

use App\Models\BlockedIp;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class CheckBlockedIp
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Schema::hasTable('blocked_ips') && BlockedIp::where('ip_address', $request->ip())->exists()) {
            abort(403, 'Akses Anda diblokir karena terlalu banyak percobaan login yang gagal.');
        }

        return $next($request);
    }
}
