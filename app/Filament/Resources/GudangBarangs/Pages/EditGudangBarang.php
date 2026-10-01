<?php

namespace App\Filament\Resources\GudangBarangs\Pages;

use App\Filament\Resources\GudangBarangs\GudangBarangResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditGudangBarang extends EditRecord
{
    protected static string $resource = GudangBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
