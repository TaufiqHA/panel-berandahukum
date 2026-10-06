<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Http\Middleware\CheckBlockedIp;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->sidebarFullyCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->resourceEditPageRedirect('index')
            ->brandName('Melindastore')
            ->colors([
                'primary' => Color::Indigo,
                'secondary' => Color::Indigo,
            ])
            ->defaultThemeMode(ThemeMode::Light)
            ->darkMode(false)
            ->navigationGroups([
                NavigationGroup::make()->label('Master'),
                NavigationGroup::make()->label('Barang'),
                NavigationGroup::make()
                    ->label('Pindah Toko')
                    ->collapsible()
                    ->collapsed(),
                NavigationGroup::make()->label('Transaction'),
                NavigationGroup::make()->label('Report'),
                NavigationGroup::make()->label('User Management'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): string => Blade::render(<<<'HTML'
                    <style>
                        .fi-ta-filters-above-content-ctn .fi-ta-filters {
                            display: flex;
                            flex-wrap: wrap;
                            align-items: flex-end;
                            column-gap: 0.75rem;
                            row-gap: 0.75rem;
                        }
                        .fi-ta-filters-above-content-ctn .fi-ta-filters-header {
                            flex: 0 0 100%;
                        }
                        .fi-ta-filters-above-content-ctn .fi-ta-filters > .fi-grid {
                            flex: 1 1 auto;
                        }
                        .fi-ta-filters-above-content-ctn .fi-ta-filters-actions {
                            flex: 0 0 auto;
                        }
                    </style>
                    HTML),
            )
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                //
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                CheckBlockedIp::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
