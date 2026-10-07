<?php

namespace App\Filament\Resources\WhitelistedIps\Pages;

use App\Filament\Resources\WhitelistedIps\WhitelistedIpResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWhitelistedIp extends CreateRecord
{
    protected static string $resource = WhitelistedIpResource::class;
}
