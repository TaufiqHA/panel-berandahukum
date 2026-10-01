<?php

namespace App\Filament\Resources\PindahGudangs\Schemas;

use App\Filament\Support\LineItems;
use App\Models\GudangBarang;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PindahGudangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Pindah Toko')->schema([
                    Grid::make(12)->schema([
                        DatePicker::make('date')
                            ->label('Tanggal')
                            ->default(now())
                            ->columnSpan(3)
                            ->required(),
                        TextInput::make('no_ref')
                            ->label('No. Referensi')
                            ->maxLength(255)
                            ->columnSpan(3),
                        Select::make('from')
                            ->label('Dari Toko')
                            ->relationship('toko', 'nama_toko')
                            ->wrapOptionLabels(false)->searchable()
                            ->preload()
                            ->columnSpan(3)
                            ->required()
                            ->live(),
                        Select::make('to')
                            ->label('Ke Toko')
                            ->relationship('toko_to', 'nama_toko')
                            ->wrapOptionLabels(false)->searchable()
                            ->preload()
                            ->columnSpan(3)
                            ->required(),
                    ]),
                ])->columns(1),

                Section::make('Detail Barang')->schema([
                    Repeater::make('items')
                        ->hiddenLabel()
                        ->addActionLabel('Tambah Barang')
                        ->schema([
                            Select::make('gudang_barang_id')
                                ->label('Barang / Serial Number')
                                ->wrapOptionLabels(false)->searchable()
                                ->getSearchResultsUsing(function (string $search, $get): array {
                                    $from = $get('../../from');

                                    if (! $from) {
                                        return [];
                                    }

                                    return GudangBarang::query()
                                        ->with('barang')
                                        ->where('toko_id', $from)
                                        ->where('status', 1)
                                        ->where(function ($query) use ($search): void {
                                            $query->where('serial_number_id', 'like', '%'.$search.'%')
                                                ->orWhereHas('barang', fn ($barang) => $barang->where('nama_product', 'like', '%'.$search.'%'));
                                        })
                                        ->orderByDesc('id')
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(fn (GudangBarang $unit): array => [$unit->id => LineItems::unitLabel($unit)])
                                        ->all();
                                })
                                ->getOptionLabelUsing(fn ($value): ?string => ($unit = GudangBarang::withTrashed()->with('barang')->find($value)) ? LineItems::unitLabel($unit) : null)
                                ->required(),
                            TextInput::make('keterangan')
                                ->label('Keterangan')
                                ->maxLength(255),
                        ])
                        ->columns(2)
                        ->visible(fn (string $operation): bool => $operation === 'edit')
                        ->columnSpanFull(),
                ])->columns(1),
            ]);
    }
}
