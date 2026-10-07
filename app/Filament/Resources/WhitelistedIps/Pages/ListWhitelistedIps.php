<?php

namespace App\Filament\Resources\WhitelistedIps\Pages;

use App\Filament\Resources\WhitelistedIps\WhitelistedIpResource;
use App\Models\IpWhitelistSetting;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListWhitelistedIps extends ListRecords
{
    protected static string $resource = WhitelistedIpResource::class;

    public function getSubheading(): ?string
    {
        return IpWhitelistSetting::isEnabled()
            ? 'Whitelist IP sedang AKTIF. Hanya IP yang terdaftar yang dapat mengakses aplikasi.'
            : 'Whitelist IP sedang NONAKTIF. Semua IP dapat mengakses aplikasi.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('toggleWhitelist')
                ->label(fn (): string => IpWhitelistSetting::isEnabled() ? 'Nonaktifkan Whitelist' : 'Aktifkan Whitelist')
                ->icon(fn (): Heroicon => IpWhitelistSetting::isEnabled() ? Heroicon::OutlinedShieldExclamation : Heroicon::OutlinedShieldCheck)
                ->color(fn (): string => IpWhitelistSetting::isEnabled() ? 'danger' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn (): string => IpWhitelistSetting::isEnabled() ? 'Nonaktifkan Whitelist IP' : 'Aktifkan Whitelist IP')
                ->modalDescription(fn (): string => IpWhitelistSetting::isEnabled()
                    ? 'Semua IP akan diizinkan mengakses aplikasi.'
                    : 'Hanya IP yang terdaftar yang dapat mengakses aplikasi. Superadmin tetap dapat mengakses dari mana saja.')
                ->action(function (): void {
                    $enabled = ! IpWhitelistSetting::isEnabled();
                    IpWhitelistSetting::setEnabled($enabled);

                    Notification::make()
                        ->title($enabled ? 'Whitelist IP diaktifkan' : 'Whitelist IP dinonaktifkan')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
