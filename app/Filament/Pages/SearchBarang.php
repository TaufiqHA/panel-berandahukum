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
            ->query($this->searchQuery())
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
                    ->formatStateUsing(fn ($state, GudangBarang $record): string => $record->trashed()
                        ? 'Terjual'
                        : match ((int) $state) {
                            1 => 'Stock',
                            3 => 'Barang Keluar',
                            default => 'Lainnya',
                        })
                    ->color(fn ($state, GudangBarang $record): string => $record->trashed()
                        ? 'danger'
                        : match ((int) $state) {
                            1 => 'success',
                            3 => 'warning',
                            default => 'gray',
                        }),
                TextColumn::make('detail_barang_masuk.stock_in.tanggal_masuk')->limit(30)
                    ->label('Tanggal Masuk')
                    ->date('d M Y')
                    ->placeholder('-'),
                TextColumn::make('detail_penjualan.penjualan.kode_penjualan')->limit(30)
                    ->label('Kode Penjualan')
                    ->placeholder('-'),
                TextColumn::make('detail_penjualan.penjualan.nama_pembeli')->limit(30)
                    ->label('Nama Pembeli')
                    ->placeholder('-'),
                TextColumn::make('detail_penjualan.penjualan.date')->limit(30)
                    ->label('Tanggal Penjualan')
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
                        'stock' => 'Stock',
                        'barang_keluar' => 'Barang Keluar',
                        'terjual' => 'Terjual',
                    ])
                    ->query(function (Builder $query, array $data): void {
                        match ($data['value'] ?? null) {
                            'stock' => $query->whereNull('deleted_at')->where('status', 1),
                            'barang_keluar' => $query->whereNull('deleted_at')->where('status', 3),
                            'terjual' => $query->whereNotNull('deleted_at'),
                            default => null,
                        };
                    }),
            ]);
    }

    public function getTableQuery(): Builder
    {
        return $this->searchQuery();
    }

    /**
     * Sold units and the stock-in records they originated from may be
     * soft-deleted, so the history chain is loaded including trashed rows.
     */
    private function searchQuery(): Builder
    {
        return GudangBarang::query()
            ->withTrashed()
            ->with([
                'barang',
                'toko',
                'detail_penjualan.penjualan',
                'detail_barang_masuk' => fn ($query) => $query->withTrashed()->with([
                    'stock_in' => fn ($stockIn) => $stockIn->withTrashed(),
                ]),
            ]);
    }
}
