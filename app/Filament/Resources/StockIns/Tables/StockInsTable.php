<?php

namespace App\Filament\Resources\StockIns\Tables;

use App\Filament\Resources\StockIns\Pages\EditStockIn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StockInsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->limit(30)
                    ->label('Id')
                    ->sortable(),
                TextColumn::make('po.kode_po')->limit(30)
                    ->label('No PO')
                    ->placeholder('-'),
                TextColumn::make('barang.nama_product')->limit(30)
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jumlah')->limit(30)
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tanggal_masuk')->limit(30)
                    ->label('Tanggal Masuk')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('harga_beli')->limit(30)
                    ->label('Harga Beli')
                    ->money('IDR', divideBy: 1),
                TextColumn::make('harga_jual')->limit(30)
                    ->label('Harga Jual')
                    ->money('IDR', divideBy: 1),
                TextColumn::make('price_list')->limit(30)
                    ->label('Price List')
                    ->money('IDR', divideBy: 1),
                TextColumn::make('made_in')->limit(30)
                    ->label('Made In'),
                TextColumn::make('supplier')->limit(30)
                    ->label('Supplier'),
                TextColumn::make('toko.nama_toko')->limit(30)
                    ->label('Toko')
                    ->sortable(),
                TextColumn::make('keterangan')->limit(30)
                    ->label('Keterangan')
                    ->limit(30),
            ])
            ->defaultSort('tanggal_masuk', 'desc')
            ->filters([
                Filter::make('tanggal_masuk')
                    ->columnSpan(2)
                    ->columns(2)
                    ->label('Tanggal')
                    ->form([
                        DatePicker::make('date_from')->label('Tanggal Awal'),
                        DatePicker::make('date_to')->label('Tanggal Akhir'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['date_from'], fn (Builder $q, $date) => $q->whereDate('tanggal_masuk', '>=', $date))
                            ->when($data['date_to'], fn (Builder $q, $date) => $q->whereDate('tanggal_masuk', '<=', $date));
                    }),
                SelectFilter::make('toko_id')
                    ->label('Toko')
                    ->relationship('toko', 'nama_toko')
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ])
            ->filtersFormColumns(3)
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->after(fn ($record) => EditStockIn::deleteStockUnits($record->id)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
