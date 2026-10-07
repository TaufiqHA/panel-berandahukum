<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\ChecksMenuPermission;
use App\Models\Barang;
use App\Models\GudangBarang;
use App\Models\Kategori;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
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
            ->query(
                GudangBarang::query()
                    ->with([
                        'barang',
                        'toko',
                        'detail_barang_masuk' => fn ($query) => $query->withTrashed()->with([
                            'stock_in' => fn ($stockIn) => $stockIn->withTrashed(),
                        ]),
                    ])
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
                TextColumn::make('barang.merk')->limit(30)
                    ->label('Merk')
                    ->searchable(),
                TextColumn::make('barang.warna')->limit(30)
                    ->label('Warna'),
                TextColumn::make('barang.satuan')->limit(30)
                    ->label('Satuan'),
                TextColumn::make('barang.ukuran')->limit(30)
                    ->label('Ukuran'),
                TextColumn::make('toko.nama_toko')->limit(30)
                    ->label('Toko')
                    ->sortable(),
                TextColumn::make('stok')
                    ->label('Stok')
                    ->badge()
                    ->color('success')
                    ->state(fn (): int => 1),
                TextColumn::make('detail_barang_masuk.stock_in.id')->limit(30)
                    ->label('ID Barang Masuk')
                    ->placeholder('-'),
                TextColumn::make('detail_barang_masuk.stock_in.tanggal_masuk')->limit(30)
                    ->label('Tanggal Masuk')
                    ->date('d M Y')
                    ->placeholder('-'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('barang_id')
                    ->label('Nama Barang')
                    ->relationship('barang', 'nama_product')
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
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        $merk = $data['value'] ?? null;

                        if (blank($merk)) {
                            return $query;
                        }

                        return $query->whereHas(
                            'barang',
                            fn (Builder $query): Builder => $query->where('merk', $merk),
                        );
                    }),
                SelectFilter::make('toko_id')
                    ->label('Toko')
                    ->relationship('toko', 'nama_toko')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options(fn (): array => Kategori::query()
                        ->orderBy('nama_kategori')
                        ->pluck('nama_kategori', 'id')
                        ->all())
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        $kategoriId = $data['value'] ?? null;

                        if (blank($kategoriId)) {
                            return $query;
                        }

                        return $query->whereHas(
                            'barang.kategori',
                            fn (Builder $query): Builder => $query->whereKey($kategoriId),
                        );
                    }),
                SelectFilter::make('stok')
                    ->label('Stok')
                    ->options([
                        'eq0' => '0',
                        '1-5' => '1 - 5',
                        '6-20' => '6 - 20',
                        'gt20' => '> 20',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $condition = match ($data['value'] ?? null) {
                            'eq0' => '= 0',
                            '1-5' => 'between 1 and 5',
                            '6-20' => 'between 6 and 20',
                            'gt20' => '> 20',
                            default => null,
                        };

                        if ($condition === null) {
                            return $query;
                        }

                        return $query->whereRaw(static::stokSubquery().' '.$condition);
                    }),
            ])
            ->filtersFormColumns(3)
            ->filtersLayout(FiltersLayout::AboveContent);
    }

    /**
     * Correlated subquery counting available stock for the row's barang and toko.
     */
    private static function stokSubquery(): string
    {
        return '(select count(*) from gudang_barangs as gb_stok'
            .' where gb_stok.barang_id = gudang_barangs.barang_id'
            .' and (gb_stok.toko_id = gudang_barangs.toko_id or (gb_stok.toko_id is null and gudang_barangs.toko_id is null))'
            .' and gb_stok.status = 1 and gb_stok.deleted_at is null)';
    }
}
