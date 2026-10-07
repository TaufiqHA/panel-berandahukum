<?php

namespace App\Filament\Resources\WhitelistedIps\Pages;

use App\Filament\Resources\WhitelistedIps\WhitelistedIpResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWhitelistedIp extends EditRecord
{
    protected static string $resource = WhitelistedIpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
