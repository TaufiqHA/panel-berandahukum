<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('toko_id')
                    ->label('Toko')
                    ->relationship('toko', 'nama_toko')
                    ->wrapOptionLabels(false)->searchable()
                    ->preload()
                    ->default(null),
                Textarea::make('cara_pembayaran')
                    ->label('Cara Pembayaran')
                    ->rows(8)
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
