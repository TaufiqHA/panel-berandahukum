<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class MaintenanceController extends Controller
{
    public function clearCache(): string
    {
        Artisan::call('optimize:clear');
        $output = Artisan::output();

        Artisan::call('filament:optimize-clear');
        $output .= Artisan::output();

        return '<pre>'.e($output).'</pre>';
    }
}
