<?php

namespace App\Filament\Resources\Tokos\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TokoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('nama_toko')
                    ->label('Nama Toko')
                    ->required()
                    ->maxLength(255),
                Textarea::make('alamat_toko')
                    ->label('Alamat Toko')
                    ->default(null)
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label('Logo / Gambar Toko')
                    ->image()
                    ->disk('uploads')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->imageEditor()
                    ->columnSpanFull(),
            ]);
    }
}
