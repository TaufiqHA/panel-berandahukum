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
                TextColumn::make('status')->limit(30)
                    ->label('Stok')
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
                TextColumn::make('detail_barang_masuk.stock_in.id')->limit(30)
                    ->label('Id'),
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
                SelectFilter::make('status')
                    ->label('Stok')
                    ->options([
                        1 => 'Stock',
                        3 => 'Barang Keluar',
                    ]),
            ])
            ->filtersFormColumns(3)
            ->filtersLayout(FiltersLayout::AboveContent);
    }
}
