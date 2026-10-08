<?php

namespace App\Filament\Resources\StockIns\Schemas;

use App\Models\Barang;
use App\Models\Supplier;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class StockInForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Data Stock')->schema([
                    Grid::make(12)->schema([
                        Select::make('barang_id')
                            ->label('Nama Barang')
                            ->relationship('barang', 'nama_product')
                            ->getOptionLabelFromRecordUsing(fn ($record): string => $record->nama_product.($record->warna ? ' - '.$record->warna : ''))
                            ->wrapOptionLabels(false)->searchable(['nama_product', 'merk'])
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn ($state, Set $set) => $set('price_list', number_format((float) (Barang::find($state)?->harga ?? 0), 0, ',', '.')))
                            ->columnSpanFull()
                            ->required(),
                        TextInput::make('jumlah')
                            ->label('Jumlah')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->columnSpan(4)
                            ->required(),
                        DatePicker::make('tanggal_masuk')
                            ->label('Tanggal Masuk')
                            ->default(now())
                            ->columnSpan(4)
                            ->required(),
                        Select::make('supplier')
                            ->label('Supplier')
                            ->options(fn (): array => Supplier::query()
                                ->orderBy('nama_supplier')
                                ->pluck('nama_supplier', 'nama_supplier')
                                ->all())
                            ->searchable()
                            ->columnSpan(4),
                        TextInput::make('harga_beli')
                            ->label('Harga Beli')
                            ->money()
                            ->default(0)
                            ->columnSpan(4),
                        TextInput::make('price_list')
                            ->label('Price List')
                            ->prefix('Rp')
                            ->default(0)
                            ->disabled()
                            ->dehydrated()
                            ->formatStateUsing(fn ($state, $get): ?string => number_format((float) preg_replace('/[^0-9-]/', '', (string) (Barang::find($get('barang_id'))?->harga ?? $state ?? 0)), 0, ',', '.'))
                            ->dehydrateStateUsing(fn ($state): float => (float) preg_replace('/[^0-9-]/', '', (string) $state))
                            ->columnSpan(4),
                        TextInput::make('made_in')
                            ->label('Made In')
                            ->maxLength(255)
                            ->columnSpan(4),
                        Select::make('toko_id')
                            ->label('Toko')
                            ->relationship('toko', 'nama_toko')
                            ->wrapOptionLabels(false)->searchable()
                            ->preload()
                            ->default(fn () => auth()->user()?->toko_id)
                            ->disabled(fn () => (int) (auth()->user()?->status) === 2)
                            ->dehydrated()
                            ->columnSpan(4)
                            ->required(),
                        Select::make('type_serial_number')
                            ->label('Serial Number')
                            ->options([
                                1 => 'All',
                                2 => 'One by one',
                            ])
                            ->default(2)
                            ->columnSpan(4)
                            ->required()
                            ->live(),
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
                ])->columns(1),

                Section::make('Detail Barang')->schema([
                    Textarea::make('serial_all')
                        ->label('Serial Number (pisahkan dengan spasi / koma / baris baru)')
                        ->rows(4)
                        ->visible(fn ($get, string $operation): bool => $operation === 'create' && (int) $get('type_serial_number') === 1)
                        ->columnSpanFull(),
                    Repeater::make('serial_items')
                        ->label('Serial Number per Unit')
                        ->addActionLabel('Tambah Serial Number')
                        ->schema([
                            TextInput::make('serial_number')
                                ->label('Serial Number')
                                ->maxLength(255),
                        ])
                        ->columns(1)
                        ->grid(['default' => 1, 'sm' => 2, 'md' => 3, 'xl' => 5])
                        ->visible(fn ($get, string $operation): bool => $operation === 'edit' || (int) $get('type_serial_number') === 2)
                        ->columnSpanFull(),
                ])->columns(1),
            ]);
    }
}
