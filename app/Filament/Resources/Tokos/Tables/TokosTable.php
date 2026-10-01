<?php

namespace App\Filament\Resources\Tokos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TokosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->limit(30)
                    ->label('Id')
                    ->sortable(),
                TextColumn::make('nama_toko')->limit(30)
                    ->label('Nama Toko')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('alamat_toko')->limit(30)
                    ->label('Alamat Toko')
                    ->limit(50)
                    ->searchable(),
                ImageColumn::make('image')
                    ->label('Logo Toko')
                    ->disk('uploads')
                    ->height(40),
            ])
            ->filters([
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
