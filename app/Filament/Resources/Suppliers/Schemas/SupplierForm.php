<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('nama_supplier')
                    ->label('Nama Supplier')
                    ->required()
                    ->maxLength(255),
                TextInput::make('sales')
                    ->label('Nama Sales')
                    ->default(null)
                    ->maxLength(255),
                Textarea::make('alamat')
                    ->label('Alamat')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
