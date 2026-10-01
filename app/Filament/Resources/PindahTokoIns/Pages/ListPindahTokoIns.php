<?php

namespace App\Filament\Resources\PindahTokoIns\Pages;

use App\Filament\Resources\PindahTokoIns\PindahTokoInResource;
use Filament\Resources\Pages\ListRecords;

class ListPindahTokoIns extends ListRecords
{
    protected static string $resource = PindahTokoInResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
