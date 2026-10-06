<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    /**
     * Simple browser index linking to every maintenance action.
     */
    public function index(Request $request): Response
    {
        $this->authorizeMaintenance($request);

        $links = [
            'Clear Cache' => route('maintenance.clear-cache', $this->tokenParameter()),
            'Migrate' => route('maintenance.migrate', $this->tokenParameter()),
            'Migrate Status' => route('maintenance.migrate-status', $this->tokenParameter()),
            'Optimize' => route('maintenance.optimize', $this->tokenParameter()),
            'Optimize Clear' => route('maintenance.optimize-clear', $this->tokenParameter()),
            'Storage Link' => route('maintenance.storage-link', $this->tokenParameter()),
        ];

        $html = '<h1>Maintenance</h1><ul>';

        foreach ($links as $label => $url) {
            $html .= '<li><a href="'.e($url).'">'.e($label).'</a></li>';
        }

        $html .= '</ul>';

        return new Response($html);
    }

    public function clearCache(Request $request): Response
    {
        $this->authorizeMaintenance($request);

        return $this->runCommands(array_fill_keys(self::CACHE_COMMANDS, []));
    }

    public function migrate(Request $request): Response
    {
        $this->authorizeMaintenance($request);

        return $this->runCommands(['migrate' => ['--force' => true]]);
    }

    public function migrateStatus(Request $request): Response
    {
        $this->authorizeMaintenance($request);

        return $this->runCommands(['migrate:status' => []]);
    }

    public function optimize(Request $request): Response
    {
        $this->authorizeMaintenance($request);

        return $this->runCommands(['optimize' => []]);
    }

    public function optimizeClear(Request $request): Response
    {
        $this->authorizeMaintenance($request);

        return $this->runCommands(['optimize:clear' => []]);
    }

    public function storageLink(Request $request): Response
    {
        $this->authorizeMaintenance($request);

        return $this->runCommands(['storage:link' => []]);
    }

    /**
     * Run each whitelisted Artisan command and return the captured output.
     *
     * @param  array<string, array<string, mixed>>  $commands
     */
    private function runCommands(array $commands): Response
    {
        $registered = Artisan::all();
        $output = '';

        foreach ($commands as $command => $parameters) {
            if (! array_key_exists($command, $registered)) {
                $output .= "$ {$command}\n  skipped (command not registered)\n\n";

                continue;
            }

            try {
                Artisan::call($command, $parameters);
                $output .= "$ {$command}\n".Artisan::output()."\n";
            } catch (\Throwable $exception) {
                $output .= "$ {$command}\n  failed: {$exception->getMessage()}\n\n";
            }
        }

        return new Response('<pre>'.e($output).'</pre>');
    }

    /**
     * Only a logged in full-admin (status = 1) may run maintenance, and the
     * shared token must match when one is configured.
     */
    private function authorizeMaintenance(Request $request): void
    {
        $user = $request->user();

        abort_unless($user && (int) $user->status === 1, 403);

        $token = config('app.maintenance_token');

        if (! empty($token)) {
            abort_unless(
                hash_equals((string) $token, (string) $request->query('token')),
                403,
            );
        }
    }

    /**
     * @return array<string, string>
     */
    private function tokenParameter(): array
    {
        $token = config('app.maintenance_token');

        return empty($token) ? [] : ['token' => (string) $token];
    }
}
