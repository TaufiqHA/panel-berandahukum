<?php

namespace App\Filament\Resources\Quotations\Tables;

use App\Models\QuotationDetail;
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

class QuotationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->limit(30)
                    ->label('Id')
                    ->sortable(),
                TextColumn::make('date')->limit(30)
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('kode_quotation')->limit(30)
                    ->label('Kode Quotation')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_pembeli')->limit(30)
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('alamat_pembeli')->limit(30)
                    ->label('Alamat')
                    ->limit(30),
                TextColumn::make('telepon')->limit(30)
                    ->label('Telepon')
                    ->placeholder('-'),
                TextColumn::make('subtotal')->limit(30)
                    ->label('Total Pembayaran')
                    ->money('IDR', divideBy: 1)
                    ->sortable(),
                TextColumn::make('status')->limit(30)
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => (int) $state === 2 ? 'Draft' : 'Done')
                    ->color(fn ($state): string => (int) $state === 2 ? 'gray' : 'success'),
                TextColumn::make('toko.nama_toko')->limit(30)
                    ->label('Toko')
                    ->sortable(),
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
                    ->url(fn ($record): string => route('print.quotation', $record)),
                EditAction::make(),
                DeleteAction::make()
                    ->after(fn ($record) => QuotationDetail::where('quotation_id', $record->id)->delete()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
