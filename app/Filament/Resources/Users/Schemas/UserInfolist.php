<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('name')
                    ->label('Nama'),
                TextEntry::make('email')
                    ->label('Email'),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(function ($state, $record): string {
                        if ((int) $record->status === 1 && (int) $record->status_admin === 1) {
                            return 'Admin Pusat';
                        }

                        return (int) $record->status === 1 ? 'Superadmin' : 'Admin';
                    }),
                TextEntry::make('toko.nama_toko')
                    ->label('Toko')
                    ->placeholder('-'),
                TextEntry::make('user_menu')
                    ->label('Menu')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
