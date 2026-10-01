<?php

namespace App\Filament\Pages\Reports;

class StockReport extends ReportPage
{
    protected static string $reportType = 'stock';

    protected static ?string $navigationLabel = 'Laporan Stock';

    protected static ?string $title = 'Laporan Stock';

    protected static ?int $navigationSort = 4;

    protected static array $filterFields = [
        ['key' => 'nama_barang', 'label' => 'Barang', 'type' => 'multiselect', 'source' => 'barang'],
        ['key' => 'nama_toko', 'label' => 'Toko', 'type' => 'multiselect', 'source' => 'toko'],
    ];
}
