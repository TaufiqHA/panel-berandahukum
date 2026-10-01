<?php

namespace App\Filament\Resources\PindahGudangs\Pages;

use App\Filament\Resources\PindahGudangs\PindahGudangResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePindahGudang extends CreateRecord
{
    protected static string $resource = PindahGudangResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['items']);
        $data['status'] = 1;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
