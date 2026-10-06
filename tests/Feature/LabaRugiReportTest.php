<?php

use App\Http\Controllers\ReportController;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('laporan laba rugi berdasarkan penjualan memakai label Cara Bayar dan Status Bayar', function () {
    $report = app(ReportController::class)->labaRugi(['jenis_report' => '2']);

    expect($report['headings'])->toBe([
        'No',
        'Tanggal',
        'Kode Penjualan',
        'Nama Pembeli',
        'Cara Bayar',
        'Nama Toko',
        'Status Bayar',
        'Total Pembayaran',
        'DP',
        'Sisa',
        'Total Hrg.Beli',
        'Keuntungan',
        'Status',
        'Nama Project',
    ]);
});
