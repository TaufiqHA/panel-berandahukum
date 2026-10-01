<?php

namespace App\Filament\Resources\GudangBarangs\Pages;

use App\Filament\Resources\GudangBarangs\GudangBarangResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGudangBarangs extends ListRecords
{
    protected static string $resource = GudangBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
