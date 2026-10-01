<?php

namespace App\Filament\Resources\PindahGudangs\Pages;

use App\Filament\Resources\PindahGudangs\PindahGudangResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPindahGudangs extends ListRecords
{
    protected static string $resource = PindahGudangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
