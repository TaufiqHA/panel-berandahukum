<?php

namespace App\Filament\Resources\Barangs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('kategori_id')
                    ->label('Kategori Barang')
                    ->relationship('kategori', 'nama_kategori')
                    ->wrapOptionLabels(false)->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('nama_product')
                    ->label('Nama Barang')
                    ->required()
                    ->maxLength(255),
                TextInput::make('merk')
                    ->label('Merk')
                    ->default(null)
                    ->maxLength(255),
                TextInput::make('satuan')
                    ->label('Satuan')
                    ->default(null)
                    ->maxLength(100),
                TextInput::make('warna')
                    ->label('Warna')
                    ->default(null)
                    ->maxLength(100),
                TextInput::make('berat')
                    ->label('Berat')
                    ->default(null)
                    ->maxLength(100),
                TextInput::make('ukuran')
                    ->label('Ukuran')
                    ->default(null)
                    ->maxLength(100),
                TextInput::make('harga')
                    ->label('Harga')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),
                Toggle::make('wajib_serial_number')
                    ->label('Wajib Serial Number'),
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
