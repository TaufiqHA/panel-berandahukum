<?php

namespace App\Filament\Resources\Barangs\Tables;

use App\Models\Barang;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->limit(30)
                    ->label('Id')
                    ->sortable(),
                TextColumn::make('nama_product')->limit(30)
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('merk')->limit(30)
                    ->label('Merk')
                    ->searchable(),
                TextColumn::make('satuan')->limit(30)
                    ->label('Satuan'),
                TextColumn::make('warna')->limit(30)
                    ->label('Warna'),
                TextColumn::make('berat')->limit(30)
                    ->label('Berat'),
                TextColumn::make('ukuran')->limit(30)
                    ->label('Ukuran'),
                TextColumn::make('harga')->limit(30)
                    ->label('Price List')
                    ->money('IDR', divideBy: 1)
                    ->sortable(),
                IconColumn::make('wajib_serial_number')
                    ->label('Wajib Serial Number')
                    ->boolean(),
                TextColumn::make('keterangan')->limit(30)
                    ->label('Keterangan')
                    ->limit(30)
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama_kategori')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('merk')
                    ->label('Merk')
                    ->options(fn (): array => Barang::query()
                        ->whereNotNull('merk')
                        ->distinct()
                        ->orderBy('merk')
                        ->pluck('merk', 'merk')
                        ->all())
                    ->searchable(),
            ])
            ->filtersFormColumns(2)
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
