<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\ChecksMenuPermission;
use App\Models\Barang;
use App\Models\GudangBarang;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class SearchBarang extends Page implements HasTable
{
    use ChecksMenuPermission;

    protected static function menuPermission(): ?string
    {
        return 'Search';
    }

    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static string|UnitEnum|null $navigationGroup = 'Barang';

    protected static ?string $navigationLabel = 'Search';

    protected static ?int $navigationSort = 5;

    protected static ?string $title = 'Search Barang / Serial Number';

    protected string $view = 'filament.pages.search-barang';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                GudangBarang::query()
                    ->with(['barang', 'toko', 'detail_barang_masuk.stock_in'])
            )
            ->columns([
                TextColumn::make('barang.nama_product')->limit(30)
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('serial_number_id')->limit(30)
                    ->label('Serial Number')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('toko.nama_toko')->limit(30)
                    ->label('Toko')
                    ->sortable(),
                TextColumn::make('status')->limit(30)
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => match ((int) $state) {
                        1 => 'Stock',
                        3 => 'Barang Keluar',
                        default => 'Lainnya',
                    })
                    ->color(fn ($state): string => match ((int) $state) {
                        1 => 'success',
                        3 => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('detail_barang_masuk.stock_in.tanggal_masuk')->limit(30)
                    ->label('Tanggal Masuk')
                    ->date('d M Y')
                    ->placeholder('-'),
                TextColumn::make('detail_barang_masuk.stock_in.harga_beli')->limit(30)
                    ->label('Harga Beli')
                    ->money('IDR', divideBy: 1, decimalPlaces: 0)
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('detail_barang_masuk.stock_in.harga_jual')->limit(30)
                    ->label('Harga Jual')
                    ->money('IDR', divideBy: 1, decimalPlaces: 0)
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('detail_barang_masuk.stock_in.price_list')->limit(30)
                    ->label('Price List')
                    ->money('IDR', divideBy: 1, decimalPlaces: 0)
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('barang_id')
                    ->label('Nama Barang')
                    ->options(fn () => Barang::query()->limit(500)->pluck('nama_product', 'id'))
                    ->searchable(),
                SelectFilter::make('toko_id')
                    ->label('Toko')
                    ->relationship('toko', 'nama_toko')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        1 => 'Stock',
                        3 => 'Barang Keluar',
                    ]),
            ]);
    }

    public function getTableQuery(): Builder
    {
        return GudangBarang::query();
    }
}
