<?php

namespace App\Filament\Pages\Reports;

class PenjualanReport extends ReportPage
{
    protected static string $reportType = 'penjualan';

    protected static ?string $navigationLabel = 'Laporan Penjualan';

    protected static ?string $title = 'Laporan Penjualan';

    protected static ?int $navigationSort = 2;

    protected static array $filterFields = [
        ['key' => 'jenis_report', 'label' => 'Jenis Laporan', 'type' => 'select', 'choices' => ['1' => 'Berdasarkan Barang', '2' => 'Berdasarkan Penjualan']],
        ['key' => 'nama_barang', 'label' => 'Barang', 'type' => 'multiselect', 'source' => 'barang'],
        ['key' => 'tanggalAwal', 'label' => 'Tanggal Awal', 'type' => 'date'],
        ['key' => 'tanggalAkhir', 'label' => 'Tanggal Akhir', 'type' => 'date'],
        ['key' => 'nama_toko', 'label' => 'Toko', 'type' => 'multiselect', 'source' => 'toko'],
        ['key' => 'status_bayar', 'label' => 'Status Bayar', 'type' => 'select', 'choices' => ['Lunas' => 'Lunas', 'DP' => 'DP']],
    ];
}
