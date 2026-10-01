<?php

namespace App\Filament\Pages\Reports;

class LabaRugiReport extends ReportPage
{
    protected static string $reportType = 'laba-rugi';

    protected static ?string $navigationLabel = 'Laporan Laba Rugi';

    protected static ?string $title = 'Laporan Laba Rugi';

    protected static ?int $navigationSort = 5;

    protected static array $filterFields = [
        ['key' => 'jenis_report', 'label' => 'Jenis Laporan', 'type' => 'select', 'choices' => ['1' => 'Berdasarkan Barang', '2' => 'Berdasarkan Penjualan']],
        ['key' => 'nama_barang', 'label' => 'Barang', 'type' => 'multiselect', 'source' => 'barang'],
        ['key' => 'tanggalAwal', 'label' => 'Tanggal Awal', 'type' => 'date'],
        ['key' => 'tanggalAkhir', 'label' => 'Tanggal Akhir', 'type' => 'date'],
        ['key' => 'nama_toko', 'label' => 'Toko', 'type' => 'multiselect', 'source' => 'toko'],
    ];
}
