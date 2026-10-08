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
                    ->formatStateUsing(fn (GudangBarang $record): string => match (true) {
                        $record->resolvePenjualan() !== null => 'Terjual',
                        $record->trashed() => 'Dihapus',
                        (int) $record->status === 1 => 'Stock',
                        (int) $record->status === 3 => 'Barang Keluar',
                        default => 'Lainnya',
                    })
                    ->color(fn (GudangBarang $record): string => match (true) {
                        $record->resolvePenjualan() !== null => 'danger',
                        $record->trashed() => 'gray',
                        (int) $record->status === 1 => 'success',
                        (int) $record->status === 3 => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('detail_barang_masuk.stock_in.tanggal_masuk')->limit(30)
                    ->label('Tanggal Masuk')
                    ->date('d M Y')
                    ->placeholder('-'),
                TextColumn::make('detail_penjualan.penjualan.kode_penjualan')->limit(30)
                    ->label('Kode Penjualan')
                    ->state(fn (GudangBarang $record): ?string => $record->resolvePenjualan()?->kode_penjualan)
                    ->placeholder('-'),
                TextColumn::make('detail_penjualan.penjualan.nama_pembeli')->limit(30)
                    ->label('Nama Pembeli')
                    ->state(fn (GudangBarang $record): ?string => $record->resolvePenjualan()?->nama_pembeli)
                    ->placeholder('-'),
                TextColumn::make('detail_penjualan.penjualan.date')->limit(30)
                    ->label('Tanggal Penjualan')
                    ->state(fn (GudangBarang $record): ?string => $record->resolvePenjualan()?->date)
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
                            'terjual' => $this->whereSold($query->whereNotNull('deleted_at')),
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
     * Only treat a unit as sold when it actually links to a sale, either through
     * its sale detail or through a legacy orphaned detail row.
     */
    private function whereSold(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query->whereHas('detail_penjualan.penjualan')
                ->orWhereExists(function ($sub): void {
                    $sub->selectRaw('1')
                        ->from('detail_penjualans')
                        ->join('penjualans', 'penjualans.id', '=', 'detail_penjualans.penjualan_id')
                        ->whereNull('penjualans.deleted_at')
                        ->whereColumn('detail_penjualans.barang_id', 'gudang_barangs.barang_id')
                        ->whereColumn('detail_penjualans.created_at', 'gudang_barangs.deleted_at')
                        ->where(fn ($sub) => $sub->whereNull('detail_penjualans.gudang_barang_id')
                            ->orWhere('detail_penjualans.gudang_barang_id', 0));
                });
        });
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
