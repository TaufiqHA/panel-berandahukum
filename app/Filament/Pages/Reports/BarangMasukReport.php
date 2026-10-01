<?php

namespace App\Filament\Pages\Reports;

class BarangMasukReport extends ReportPage
{
    protected static string $reportType = 'barang-masuk';

    protected static ?string $navigationLabel = 'Laporan Barang Masuk';

    protected static ?string $title = 'Laporan Barang Masuk';

    protected static ?int $navigationSort = 1;

    protected static array $filterFields = [
        ['key' => 'nama_barang', 'label' => 'Barang', 'type' => 'multiselect', 'source' => 'barang'],
        ['key' => 'tanggalAwal', 'label' => 'Tanggal Awal', 'type' => 'date'],
        ['key' => 'tanggalAkhir', 'label' => 'Tanggal Akhir', 'type' => 'date'],
        ['key' => 'nama_toko', 'label' => 'Toko', 'type' => 'multiselect', 'source' => 'toko'],
    ];
}
