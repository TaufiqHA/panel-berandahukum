<?php

namespace App\Filament\Resources\Barangs\Pages;

use App\Filament\Resources\Barangs\BarangResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBarangs extends ListRecords
{
    protected static string $resource = BarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->url(fn (): string => route('barang.export.pdf', array_filter([
                    'search' => $this->tableSearch,
                    'nama_product' => $this->tableFilters['nama_product']['value'] ?? null,
                    'kategori_id' => $this->tableFilters['kategori_id']['value'] ?? null,
                    'merk' => $this->tableFilters['merk']['value'] ?? null,
                ]))),
            CreateAction::make(),
        ];
    }
}
