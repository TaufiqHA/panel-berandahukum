<?php

namespace App\Filament\Resources\WhitelistedIps\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WhitelistedIpForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('ip_address')
                    ->label('IP Address')
                    ->required()
                    ->ip()
                    ->maxLength(45)
                    ->unique(ignoreRecord: true)
                    ->helperText('Contoh: 192.168.1.10 atau 2001:db8::1')
                    ->placeholder('0.0.0.0'),
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->default(null)
                    ->rows(3)
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }
}
