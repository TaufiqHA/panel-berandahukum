<?php

namespace App\Filament\Resources\GudangBarangs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GudangBarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('barang_id')
                    ->label('Nama Barang')
                    ->relationship('barang', 'nama_product')
                    ->wrapOptionLabels(false)->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('serial_number_id')
                    ->label('Serial Number')
                    ->maxLength(255),
                Select::make('toko_id')
                    ->label('Toko')
                    ->relationship('toko', 'nama_toko')
                    ->wrapOptionLabels(false)->searchable()
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        1 => 'Tersedia',
                        3 => 'Dipindah',
                    ])
                    ->default(1)
                    ->required(),
            ]);
    }
}
