<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckBlockedIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (\App\Models\BlockedIp::where('ip_address', $request->ip())->exists()) {
            abort(403, 'Akses Anda diblokir karena terlalu banyak percobaan login yang gagal.');
        }
        return $next($request);
    }
}
