<?php

namespace App\Filament\Pages\Reports;

class PoReport extends ReportPage
{
    protected static string $reportType = 'po';

    protected static ?string $navigationLabel = 'Laporan Purchase Order';

    protected static ?string $title = 'Laporan Purchase Order';

    protected static ?int $navigationSort = 6;

    protected static array $filterFields = [
        ['key' => 'tanggalAwal', 'label' => 'Tanggal Awal', 'type' => 'date'],
        ['key' => 'tanggalAkhir', 'label' => 'Tanggal Akhir', 'type' => 'date'],
        ['key' => 'jatuh_tempo_awal', 'label' => 'Jatuh Tempo Awal', 'type' => 'date'],
        ['key' => 'jatuh_tempo_akhir', 'label' => 'Jatuh Tempo Akhir', 'type' => 'date'],
        ['key' => 'nama_toko', 'label' => 'Toko', 'type' => 'multiselect', 'source' => 'toko'],
        ['key' => 'nama_supplier', 'label' => 'Supplier', 'type' => 'multiselect', 'source' => 'supplier'],
        ['key' => 'status_po', 'label' => 'Status PO', 'type' => 'select', 'choices' => ['semua' => '-Pilih Semua-', '1' => 'Dikirim', '2' => 'Draft', '3' => 'Canceled']],
        ['key' => 'status_terima', 'label' => 'Status Barang', 'type' => 'select', 'choices' => ['2' => '-Pilih Semua-', '1' => 'Diterima', '0' => 'Belum Diterima']],
        ['key' => 'status_bayar', 'label' => 'Status Bayar', 'type' => 'select', 'choices' => ['3' => '-Pilih Semua-', '1' => 'Lunas', '0' => 'Hutang', '2' => 'DP']],
    ];
}
