<?php

use App\Http\Controllers\ReportController;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\StockIn;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('laporan barang masuk menampilkan kolom ID Barang Masuk', function () {
    $this->actingAs(User::factory()->create(['status' => 1]));

    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker',
        'harga' => 1000000,
    ]);

    $stockIn = StockIn::create([
        'barang_id' => $barang->id,
        'jumlah' => 2,
        'tanggal_masuk' => now()->toDateString(),
        'toko_id' => $toko->id,
    ]);

    $report = app(ReportController::class)->barangMasuk([]);

    expect($report['headings'])->toBe([
        'No',
        'ID Barang Masuk',
        'Nama Barang',
        'Jumlah',
        'Tanggal Masuk',
        'Harga Beli',
        'Harga Jual',
        'Price List',
        'Made In',
        'Supplier',
        'Toko',
        'Keterangan',
    ]);

    expect($report['rows'])->toHaveCount(1)
        ->and($report['rows'][0][1])->toBe($stockIn->id);
});
