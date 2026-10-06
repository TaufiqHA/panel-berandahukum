<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Filament\Support\LineItems;
use App\Models\Invoice;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Invoice')->schema([
                    Grid::make(12)->schema([
                        DatePicker::make('date')
                            ->label('Date')
                            ->default(now())
                            ->columnSpan(3)
                            ->required(),
                        TextInput::make('kode_invoice')
                            ->label('Ref Number')
                            ->default(fn (): string => 'INV - '.str_pad((string) ((int) Invoice::max('id') + 1), 7, '0', STR_PAD_LEFT))
                            ->columnSpan(3)
                            ->required(),
                        Select::make('toko_id')
                            ->label('Nama Toko')
                            ->relationship('toko', 'nama_toko')
                            ->wrapOptionLabels(false)->searchable()
                            ->preload()
                            ->default(fn () => auth()->user()?->toko_id)
                            ->disabled(fn () => (int) (auth()->user()?->status) === 2)
                            ->dehydrated()
                            ->live()
                            ->columnSpan(3)
                            ->required(),
                        Select::make('nama_sales')
                            ->label('Nama Sales')
                            ->options(fn (): array => LineItems::salesOptions())
                            ->wrapOptionLabels(false)->searchable()
                            ->columnSpan(3),
                    ]),
                    Grid::make(12)->schema([
                        TextInput::make('nama_pembeli')
                            ->label('Nama Pembeli')
                            ->columnSpan(4),
                        TextInput::make('alamat_pembeli')
                            ->label('Alamat Pembeli')
                            ->columnSpan(4),
                        TextInput::make('tlp_pembeli')
                            ->label('Telepon Pembeli')
                            ->columnSpan(4),
                    ]),
                    Grid::make(12)->schema([
                        Grid::make(1)->schema([
                            Toggle::make('show_option')->label('Option Text')->inline(false),
                            TextInput::make('option_text')->hiddenLabel()->placeholder('Masukan option text disini'),
                        ])->columnSpan(6),
                        Grid::make(1)->schema([
                            Toggle::make('show_project')->label('Nama Project')->inline(false),
                            TextInput::make('nama_project')->hiddenLabel()->placeholder('Masukan nama project disini'),
                        ])->columnSpan(6),
                    ]),
                ])->columns(1),

                Section::make('Detail Barang')->schema([
                    LineItems::barangRepeater(withStock: true),
                ])->columns(1),

                Section::make('Payment Detail')->schema([
                    Grid::make(12)->schema([
                        Select::make('metode_pembayaran')
                            ->label('Metode Pembayaran')
                            ->options(['Tunai' => 'Tunai', 'Transfer' => 'Transfer', 'Giro' => 'Giro'])
                            ->default('Tunai')
                            ->columnSpan(4),
                        Select::make('payment_status')
                            ->label('Status Pembayaran')
                            ->options([
                                'Cash Before Delivery (CBD)' => 'Cash Before Delivery (CBD)',
                                'Cash On Delivery (COD)' => 'Cash On Delivery (COD)',
                                'Tempo' => 'Tempo',
                                'DP' => 'DP',
                            ])
                            ->live()
                            ->columnSpan(4),
                        TextInput::make('waktu')
                            ->label('Waktu / Bulan')
                            ->visible(fn ($get): bool => $get('payment_status') === 'Tempo')
                            ->columnSpan(4),
                        Placeholder::make('subtotal_display')
                            ->label('Total')
                            ->content(fn ($get): HtmlString => new HtmlString(LineItems::rupiah(LineItems::sum($get('items')))))
                            ->columnSpan(4),
                    ]),
                    Toggle::make('use_ppn')->label('PPN')->live(),
                    Grid::make(12)->schema([
                        Grid::make(1)->schema([
                            Textarea::make('keterangan')
                                ->label('Keterangan')
                                ->rows(6),
                            Toggle::make('show_infopembayaran')->label('Cara Pembayaran')->inline(false),
                        ])->columnSpan(6),
                        Grid::make(1)->schema([
                            Placeholder::make('ppn_display')
                                ->label('PPN')
                                ->visible(fn ($get): bool => (bool) $get('use_ppn'))
                                ->content(fn ($get): HtmlString => new HtmlString(LineItems::rupiah(round(LineItems::sum($get('items')) * 0.11)))),
                            Placeholder::make('grand_total_ppn_display')
                                ->label('Grand Total')
                                ->visible(fn ($get): bool => (bool) $get('use_ppn'))
                                ->content(fn ($get): HtmlString => new HtmlString(LineItems::rupiah(LineItems::sum($get('items')) + round(LineItems::sum($get('items')) * 0.11)))),
                            TextInput::make('dp_payment')
                                ->label('DP')
                                ->money()
                                ->default(0)
                                ->live()
                                ->visible(fn ($get): bool => $get('payment_status') === 'DP'),
                            Placeholder::make('sisa_display')
                                ->label('Sisa')
                                ->visible(fn ($get): bool => $get('payment_status') === 'DP')
                                ->content(fn ($get): HtmlString => new HtmlString(LineItems::rupiah(max(0, LineItems::sum($get('items')) + ((bool) $get('use_ppn') ? round(LineItems::sum($get('items')) * 0.11) : 0) - LineItems::toNumber($get('dp_payment')))))),
                        ])->columnSpan(6),
                    ]),
                ])->columns(1),
            ]);
    }
}
