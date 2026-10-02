<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

Route::middleware('auth')->group(function () {
    Route::get('penjualan/print/{id}', [PrintController::class, 'penjualanDownload'])->name('print.penjualan');
    Route::get('penjualan/surat-jalan/{id}', [PrintController::class, 'penjualanSuratJalan'])->name('print.penjualan.surat-jalan');

    Route::get('invoice/print/{id}', [PrintController::class, 'invoicePrint'])->name('print.invoice');

    Route::get('quotation/print/{id}', [PrintController::class, 'quotationPrint'])->name('print.quotation');

    Route::get('po/print/{id}', [PrintController::class, 'poPrint'])->name('print.po');

    Route::get('barang-keluar/print/{id}', [PrintController::class, 'barangKeluarPrint'])->name('print.barang-keluar');
    Route::get('barang-keluar/surat-jalan/{id}', [PrintController::class, 'barangKeluarSuratJalan'])->name('print.barang-keluar.surat-jalan');

    Route::get('pindah-toko/out/print/{id}', [PrintController::class, 'pindahTokoOutDownload'])->name('print.pindah-toko.out');
    Route::get('pindah-toko/in/print/{id}', [PrintController::class, 'pindahTokoInDownload'])->name('print.pindah-toko.in');

    Route::get('report/export/{type}', [ReportController::class, 'export'])->name('report.export');

    Route::get('barang/export/pdf', [BarangController::class, 'exportPdf'])->name('barang.export.pdf');
});
