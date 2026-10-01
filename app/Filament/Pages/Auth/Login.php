<?php

namespace App\Filament\Pages\Auth;

use App\Models\BlockedIp;
use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    protected string $view = 'filament.auth.login';

    protected static string $layout = 'filament.auth.layout';

    /**
     * Track failed login attempts per IP and block the IP after two
     * failed attempts, mirroring the legacy authentication behaviour.
     */
    protected function throwFailureValidationException(): never
    {
        $ip = request()->ip();
        $key = 'failed_login_attempts_'.$ip;
        $attempts = (int) session($key, 0) + 1;

        session([$key => $attempts]);

        if ($attempts > 2) {
            BlockedIp::firstOrCreate(['ip_address' => $ip]);
        }

        parent::throwFailureValidationException();
    }
}
