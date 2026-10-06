<?php

namespace App\Filament\Resources\Pos\Schemas;

use App\Filament\Support\LineItems;
use App\Models\Po;
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

class PoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Purchase Order')->schema([
                    Grid::make(12)->schema([
                        DatePicker::make('date')
                            ->label('Date')
                            ->default(now())
                            ->columnSpan(3)
                            ->required(),
                        TextInput::make('kode_po')
                            ->label('Ref Number')
                            ->default(fn (): string => 'PO - '.str_pad((string) ((int) Po::max('id') + 1), 7, '0', STR_PAD_LEFT))
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
                        Select::make('nama_purchase')
                            ->label('Nama Purchasing')
                            ->options(fn (): array => LineItems::salesOptions())
                            ->wrapOptionLabels(false)->searchable()
                            ->columnSpan(3),
                    ]),
                    Grid::make(12)->schema([
                        Select::make('supplier_id')
                            ->label('Nama Supplier')
                            ->relationship('supplier', 'nama_supplier')
                            ->wrapOptionLabels(false)->searchable()
                            ->preload()
                            ->columnSpan(5),
                        Grid::make(7)->schema([
                            DatePicker::make('jatuh_tempo')
                                ->label('Tgl Jatuh Tempo')
                                ->columnSpan(5),
                            Toggle::make('show_tempo')
                                ->label('Jatuh Tempo')
                                ->inline(false)
                                ->columnSpan(2),
                        ])->columnSpan(7),
                    ]),
                ])->columns(1),

                Section::make('Detail Barang')->schema([
                    LineItems::barangRepeater(withStock: false),
                ])->columns(1),

                Section::make('Payment Detail')->schema([
                    Grid::make(12)->schema([
                        Toggle::make('use_ppn')->label('PPN')->columnSpan(6),
                        Toggle::make('status_dp')->label('DP')->live()->columnSpan(2),
                        Toggle::make('status_bayar')->label('LUNAS')->columnSpan(2),
                        Toggle::make('status_terima')->label('DITERIMA')->columnSpan(2),
                    ]),
                    Grid::make(12)->schema([
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(6)
                            ->columnSpan(6),
                        Grid::make(1)->schema([
                            Placeholder::make('total_display')
                                ->label('Total')
                                ->visible(fn ($get): bool => (bool) $get('use_ppn'))
                                ->content(fn ($get): HtmlString => new HtmlString(LineItems::rupiah(LineItems::sum($get('items'))))),
                            Placeholder::make('ppn_display')
                                ->label('PPN')
                                ->visible(fn ($get): bool => (bool) $get('use_ppn'))
                                ->content(fn ($get): HtmlString => new HtmlString(LineItems::rupiah(round(LineItems::sum($get('items')) * 0.11)))),
                            Placeholder::make('subtotal_display')
                                ->label('Grand Total')
                                ->content(fn ($get): HtmlString => new HtmlString(LineItems::rupiah(LineItems::sum($get('items')) + ((bool) $get('use_ppn') ? round(LineItems::sum($get('items')) * 0.11) : 0)))),
                            TextInput::make('po_dp')
                                ->label('Uang Muka')
                                ->money()
                                ->default(0)
                                ->live()
                                ->visible(fn ($get): bool => (bool) $get('status_dp')),
                            Placeholder::make('sisa_display')
                                ->label('Sisa Pembayaran')
                                ->visible(fn ($get): bool => (bool) $get('status_dp'))
                                ->content(fn ($get): HtmlString => new HtmlString(LineItems::rupiah(max(0, LineItems::sum($get('items')) + ((bool) $get('use_ppn') ? round(LineItems::sum($get('items')) * 0.11) : 0) - LineItems::toNumber($get('po_dp')))))),
                        ])->columnSpan(6),
                    ]),
                    Grid::make(12)->schema([
                        Textarea::make('alamat_kirim')
                            ->label('Alamat Pengiriman')
                            ->rows(4)
                            ->columnSpan(6),
                        TextInput::make('jenis_barang')
                            ->label('Jenis Barang')
                            ->columnSpan(6),
                    ]),
                ])->columns(1),
            ]);
    }
}
