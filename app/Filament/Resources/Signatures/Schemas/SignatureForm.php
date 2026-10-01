<?php

namespace App\Filament\Resources\Signatures\Schemas;

use App\Filament\Forms\Components\SignaturePad;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SignatureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Nama Sales')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                SignaturePad::make('signature')
                    ->label('Tanda Tangan')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
