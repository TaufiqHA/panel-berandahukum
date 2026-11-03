<?php

namespace App\Filament\Resources\Quotations\Tables;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Resources\Invoices\Pages\EditInvoice;
use App\Filament\Resources\Quotations\Pages\EditQuotation;
use App\Filament\Resources\Quotations\QuotationResource;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

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
                    ->money('IDR', divideBy: 1, decimalPlaces: 0)
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
                Action::make('duplicate')
                    ->label('Duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Duplicate Quotation')
                    ->modalDescription('Salin quotation ini beserta seluruh detail barangnya?')
                    ->modalSubmitActionLabel('Duplicate')
                    ->visible(fn (): bool => QuotationResource::canCreate())
                    ->action(function (Quotation $record, Component $livewire): void {
                        $duplicate = QuotationResource::duplicate($record);

                        Notification::make()
                            ->title('Quotation berhasil diduplikasi')
                            ->success()
                            ->send();

                        $livewire->redirect(EditQuotation::getUrl(['record' => $duplicate]), navigate: true);
                    }),
                Action::make('invoice')
                    ->label('Buat Invoice')
                    ->icon('heroicon-o-receipt-percent')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Buat Invoice dari Quotation')
                    ->modalDescription('Invoice akan dibuat dari quotation ini beserta seluruh detail barangnya.')
                    ->modalSubmitActionLabel('Buat Invoice')
                    ->visible(fn (Quotation $record): bool => $record->toko_id !== null
                        && InvoiceResource::canAccess()
                        && InvoiceResource::canCreate())
                    ->action(function (Quotation $record, Component $livewire): void {
                        $existingInvoice = Invoice::query()->where('quotation_id', $record->id)->first();

                        if ($existingInvoice) {
                            Notification::make()
                                ->title('Quotation sudah dibuatkan invoice')
                                ->warning()
                                ->send();

                            $livewire->redirect(EditInvoice::getUrl(['record' => $existingInvoice]), navigate: true);

                            return;
                        }

                        $invoice = QuotationResource::convertToInvoice($record);

                        Notification::make()
                            ->title('Invoice berhasil dibuat')
                            ->success()
                            ->send();

                        $livewire->redirect(EditInvoice::getUrl(['record' => $invoice]), navigate: true);
                    }),
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
