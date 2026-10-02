<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class MaintenanceController extends Controller
{
    /**
     * @var array<int, string>
     */
    private const CACHE_COMMANDS = [
        'optimize:clear',
        'filament:clear-cached-components',
        'icons:clear',
    ];

    public function clearCache(): string
    {
        $registered = Artisan::all();
        $output = '';

        foreach (self::CACHE_COMMANDS as $command) {
            if (! array_key_exists($command, $registered)) {
                $output .= "$ {$command}\n  skipped (command not registered)\n\n";

                continue;
            }

            try {
                Artisan::call($command);
                $output .= "$ {$command}\n".Artisan::output()."\n";
            } catch (\Throwable $exception) {
                $output .= "$ {$command}\n  failed: {$exception->getMessage()}\n\n";
            }
        }

        return '<pre>'.e($output).'</pre>';
    }
}
