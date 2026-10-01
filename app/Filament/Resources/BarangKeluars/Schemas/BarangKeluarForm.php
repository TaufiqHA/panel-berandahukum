<?php

namespace App\Filament\Resources\BarangKeluars\Schemas;

use App\Filament\Support\LineItems;
use App\Models\BarangKeluar;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BarangKeluarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Barang Keluar')->schema([
                    Grid::make(12)->schema([
                        DatePicker::make('date')
                            ->label('Date')
                            ->default(now())
                            ->columnSpan(3)
                            ->required(),
                        TextInput::make('kode_barang_keluar')
                            ->label('Ref Number')
                            ->default(fn (): string => 'BK - '.str_pad((string) ((int) BarangKeluar::max('id') + 1), 7, '0', STR_PAD_LEFT))
                            ->columnSpan(3)
                            ->required(),
                        Select::make('toko_id')
                            ->label('Nama Toko')
                            ->relationship('toko', 'nama_toko')
                            ->wrapOptionLabels(false)->searchable()
                            ->preload()
                            ->default(fn () => auth()->user()?->toko_id)
                            ->disabled(fn () => (int) (auth()->user()?->status) === 2)
                            ->dehydrated()
                            ->live()
                            ->columnSpan(3)
                            ->required(),
                        Select::make('nama_sales')
                            ->label('Nama Sales')
                            ->options(fn (): array => LineItems::salesOptions())
                            ->wrapOptionLabels(false)->searchable()
                            ->columnSpan(3),
                    ]),
                    Grid::make(12)->schema([
                        TextInput::make('nama_penerima')
                            ->label('Nama Penerima')
                            ->columnSpan(4),
                        TextInput::make('alamat_penerima')
                            ->label('Alamat Penerima')
                            ->columnSpan(4),
                        TextInput::make('telepon_penerima')
                            ->label('Telepon Penerima')
                            ->columnSpan(4),
                    ]),
                    Textarea::make('keterangan')
                        ->label('Keterangan')
                        ->rows(4)
                        ->columnSpanFull(),
                ])->columns(1),

                Section::make('Detail Barang')->schema([
                    LineItems::serialRepeater(withPrice: false),
                ])->columns(1),
            ]);
    }
}
