<?php

namespace App\Filament\Pages\Reports;

class BarangKeluarReport extends ReportPage
{
    protected static string $reportType = 'barang-keluar';

    protected static ?string $navigationLabel = 'Laporan Barang Keluar';

    protected static ?string $title = 'Laporan Barang Keluar';

    protected static ?int $navigationSort = 3;

    protected static array $filterFields = [
        ['key' => 'nama_barang', 'label' => 'Barang', 'type' => 'multiselect', 'source' => 'barang'],
        ['key' => 'tanggalAwal', 'label' => 'Tanggal Awal', 'type' => 'date'],
        ['key' => 'tanggalAkhir', 'label' => 'Tanggal Akhir', 'type' => 'date'],
        ['key' => 'nama_toko', 'label' => 'Toko', 'type' => 'multiselect', 'source' => 'toko'],
    ];
}
