<?php

namespace App\Filament\Resources\Penjualans\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PenjualansTable
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
                TextColumn::make('kode_penjualan')->limit(30)
                    ->label('Kode Penjualan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_pembeli')->limit(30)
                    ->label('Nama Pembeli')
                    ->searchable(),
                TextColumn::make('metode_pembayaran')->limit(30)
                    ->label('Metode Pembayaran')
                    ->placeholder('-'),
                TextColumn::make('toko.nama_toko')->limit(30)
                    ->label('Toko')
                    ->sortable(),
                TextColumn::make('payment_status')->limit(30)
                    ->label('Cara Pembayaran')
                    ->badge()
                    ->color(fn ($state): string => $state === 'Lunas' ? 'success' : ($state === 'DP' ? 'warning' : 'danger'))
                    ->placeholder('-'),
                TextColumn::make('total_pembayaran')->limit(30)
                    ->label('Total Pembayaran')
                    ->state(fn ($record): float => (float) $record->subtotal + ((float) $record->ppn > 0 ? round((float) $record->subtotal * 0.11) : 0))
                    ->money('IDR', divideBy: 1, decimalPlaces: 0),
                TextColumn::make('dp_payment')->limit(30)
                    ->label('DP')
                    ->money('IDR', divideBy: 1, decimalPlaces: 0),
                TextColumn::make('sisa')->limit(30)
                    ->label('Sisa')
                    ->money('IDR', divideBy: 1, decimalPlaces: 0),
                TextColumn::make('status')->limit(30)
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => (int) $state === 2 ? 'Draft' : 'Done')
                    ->color(fn ($state): string => (int) $state === 2 ? 'gray' : 'success'),
                TextColumn::make('nama_project')->limit(30)
                    ->label('Nama Project')
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Filter::make('date')
                    ->columnSpan(2)
                    ->columns(2)
                    ->label('Tanggal')
                    ->form([
                        DatePicker::make('date_from')->label('Tanggal Awal'),
                        DatePicker::make('date_to')->label('Tanggal Akhir'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['date_from'], fn (Builder $q, $date) => $q->whereDate('date', '>=', $date))
                        ->when($data['date_to'], fn (Builder $q, $date) => $q->whereDate('date', '<=', $date))),
                SelectFilter::make('toko_id')
                    ->label('Toko')
                    ->relationship('toko', 'nama_toko')
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ])
            ->filtersFormColumns(3)
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions([
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->openUrlInNewTab()
                    ->url(fn ($record): string => route('print.penjualan', $record)),
                Action::make('surat_jalan')
                    ->label('Surat Jalan')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->openUrlInNewTab()
                    ->url(fn ($record): string => route('print.penjualan.surat-jalan', $record)),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
