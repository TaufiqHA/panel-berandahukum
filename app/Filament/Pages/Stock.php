<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\ChecksMenuPermission;
use App\Models\Barang;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class Stock extends Page implements HasTable
{
    use ChecksMenuPermission;

    protected static function menuPermission(): ?string
    {
        return 'Stock';
    }

    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Barang';

    protected static ?string $navigationLabel = 'Stock';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Stock';

    protected string $view = 'filament.pages.stock';

    public function table(Table $table): Table
    {
        return $table
            ->query(Barang::query()->with('kategori'))
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
                TextColumn::make('total')->limit(30)
                    ->label('Stock')
                    ->badge()
                    ->color('success')
                    ->state(fn (Barang $record): int => $record->gudang_barang()->count()),
                IconColumn::make('wajib_serial_number')
                    ->label('Wajib Serial Number')
                    ->boolean(),
                TextColumn::make('keterangan')->limit(30)
                    ->label('Keterangan')
                    ->limit(30)
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('stock')
                    ->label('Stock per Toko')
                    ->icon(Heroicon::OutlinedBuildingStorefront)
                    ->color('warning')
                    ->modalHeading(fn (Barang $record): string => 'Stock per Toko — '.$record->nama_product)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (Barang $record) => view('filament.stock.per-toko', ['record' => $record])),
                Action::make('serials')
                    ->label('Serial Number')
                    ->icon(Heroicon::OutlinedSquares2x2)
                    ->color('info')
                    ->modalHeading(fn (Barang $record): string => $record->nama_product)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (Barang $record) => view('filament.stock.serials', ['record' => $record])),
            ]);
    }

    public function getTableQuery(): Builder
    {
        return Barang::query();
    }
}
