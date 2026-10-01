<?php

namespace App\Filament\Resources\PindahTokoIns\Tables;

use App\Filament\Resources\PindahGudangs\PindahGudangResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PindahTokoInsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->limit(30)
                    ->label('Id')
                    ->sortable(),
                TextColumn::make('date')->limit(30)
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('no_ref')->limit(30)
                    ->label('Ref Number')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('toko.nama_toko')->limit(30)
                    ->label('From')
                    ->sortable(),
                TextColumn::make('toko_to.nama_toko')->limit(30)
                    ->label('To')
                    ->sortable(),
                TextColumn::make('status')->limit(30)
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => (int) $state === 2 ? 'Diterima' : 'Dikirim')
                    ->color(fn ($state): string => (int) $state === 2 ? 'success' : 'warning'),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->openUrlInNewTab()
                    ->url(fn ($record): string => route('print.pindah-toko.in', $record)),
                Action::make('terima')
                    ->label('Terima')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record): bool => (int) $record->status !== 2)
                    ->requiresConfirmation()
                    ->action(function ($record): void {
                        PindahGudangResource::receive($record->id);

                        Notification::make()
                            ->title('Barang berhasil diterima')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
