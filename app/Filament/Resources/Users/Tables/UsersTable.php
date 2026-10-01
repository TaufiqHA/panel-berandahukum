<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->limit(30)
                    ->label('Id')
                    ->sortable(),
                TextColumn::make('name')->limit(30)
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')->limit(30)
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')->limit(30)
                    ->label('Status')
                    ->badge()
                    ->state(fn ($record): string => self::statusLabel($record))
                    ->color(fn ($record): string => match (self::statusLabel($record)) {
                        'Superadmin' => 'danger',
                        'Admin Pusat' => 'warning',
                        default => 'info',
                    }),
                TextColumn::make('toko.nama_toko')->limit(30)
                    ->label('Nama Toko')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('user_menu')->limit(30)
                    ->label('User Menu')
                    ->limit(40)
                    ->placeholder('-'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function statusLabel($record): string
    {
        if ((int) $record->status === 1 && (int) $record->status_admin === 1) {
            return 'Admin Pusat';
        }

        if ((int) $record->status === 1) {
            return 'Superadmin';
        }

        return 'Admin';
    }
}
