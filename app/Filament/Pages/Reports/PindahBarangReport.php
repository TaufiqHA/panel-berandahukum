<?php

namespace App\Filament\Pages\Reports;

class PindahBarangReport extends ReportPage
{
    protected static string $reportType = 'pindah-barang';

    protected static ?string $navigationLabel = 'Laporan Perpindahan Barang';

    protected static ?string $title = 'Laporan Perpindahan Barang';

    protected static ?int $navigationSort = 4;

    protected static array $filterFields = [
        ['key' => 'tanggalAwal', 'label' => 'Tanggal Awal', 'type' => 'date'],
        ['key' => 'tanggalAkhir', 'label' => 'Tanggal Akhir', 'type' => 'date'],
        ['key' => 'toko_from', 'label' => 'Toko Awal', 'type' => 'select', 'source' => 'toko'],
        ['key' => 'toko_to', 'label' => 'Toko Tujuan', 'type' => 'select', 'source' => 'toko'],
    ];
}
