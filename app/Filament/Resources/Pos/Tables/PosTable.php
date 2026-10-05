<?php

namespace App\Filament\Resources\Pos\Tables;

use App\Models\PoDetail;
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

class PosTable
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
                TextColumn::make('kode_po')->limit(30)
                    ->label('Kode PO')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_supplier')->limit(30)
                    ->label('Nama Supplier')
                    ->searchable(),
                TextColumn::make('jatuh_tempo')->limit(30)
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->placeholder('-'),
                TextColumn::make('subtotal')->limit(30)
                    ->label('Total Pembayaran')
                    ->money('IDR', divideBy: 1, decimalPlaces: 0)
                    ->sortable(),
                TextColumn::make('status')->limit(30)
                    ->label('Status PO')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => match ((int) $state) {
                        2 => 'Draft',
                        3 => 'Canceled',
                        default => 'Dikirim',
                    })
                    ->color(fn ($state): string => match ((int) $state) {
                        2 => 'gray',
                        3 => 'danger',
                        default => 'info',
                    }),
                TextColumn::make('jenis_barang')->limit(30)
                    ->label('Keterangan')
                    ->placeholder('-'),
                TextColumn::make('status_terima')->limit(30)
                    ->label('Status Barang')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => (int) $state === 1 ? 'Sudah DiTerima' : 'Belum DiTerima')
                    ->color(fn ($state): string => (int) $state === 1 ? 'success' : 'warning'),
                TextColumn::make('status_bayar')->limit(30)
                    ->label('Status Bayar')
                    ->badge()
                    ->formatStateUsing(fn ($state, $record): string => (int) $state === 1 ? 'Lunas' : ((float) $record->po_dp > 0 ? 'DP' : 'Hutang'))
                    ->color(fn ($state): string => (int) $state === 1 ? 'success' : 'warning'),
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
                SelectFilter::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'nama_supplier')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->label('Status PO')
                    ->options([1 => 'Dikirim', 2 => 'Draft', 3 => 'Canceled']),
                SelectFilter::make('status_terima')
                    ->label('Status Barang')
                    ->options([1 => 'Diterima', 0 => 'Belum Diterima']),
                SelectFilter::make('status_bayar')
                    ->label('Status Bayar')
                    ->options([1 => 'Lunas', 0 => 'Hutang', 2 => 'DP']),
            ])
            ->filtersFormColumns(7)
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions([
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->openUrlInNewTab()
                    ->url(fn ($record): string => route('print.po', $record)),
                EditAction::make(),
                DeleteAction::make()
                    ->after(fn ($record) => PoDetail::where('po_id', $record->id)->delete()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
