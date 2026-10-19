<?php

use App\Filament\Pages\Reports\StockReport;
use App\Http\Controllers\ReportController;
use App\Models\Barang;
use App\Models\DetailBarangMasuk;
use App\Models\GudangBarang;
use App\Models\Kategori;
use App\Models\StockIn;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('laporan stock menampilkan serial number dan price list tanpa kolom PO', function () {
    $this->actingAs(User::factory()->create(['status' => 1]));

    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker',
        'warna' => 'Hitam',
    ]);

    $stockIn = StockIn::create([
        'barang_id' => $barang->id,
        'jumlah' => 1,
        'tanggal_masuk' => now()->toDateString(),
        'harga_beli' => 1000000,
        'harga_jual' => 1500000,
        'price_list' => 1300000,
        'supplier' => 'Supplier A',
        'toko_id' => $toko->id,
    ]);

    $unit = GudangBarang::create([
        'barang_id' => $barang->id,
        'serial_number_id' => 'SN-001',
        'toko_id' => $toko->id,
        'status' => 1,
    ]);

    DetailBarangMasuk::create([
        'stock_in_id' => $stockIn->id,
        'gudang_barang_id' => $unit->id,
    ]);

    $report = app(ReportController::class)->stock([]);

    expect($report['headings'])->toBe([
        'No',
        'Tanggal Masuk',
        'Nama Barang',
        'Serial Number',
        'Jumlah',
        'Warna',
        'Nama Toko',
        'Harga Beli',
        'Price List',
        'Supplier',
    ]);

    expect($report['rows'])->toHaveCount(2);

    $row = $report['rows'][0];

    expect($row[3])->toBe('SN-001')
        ->and($row[8])->toBe('1.300.000');

    expect($report['rows'][1][1])->toBe('TOTAL')
        ->and($report['rows'][1][7])->toBe('1.000.000')
        ->and($report['rows'][1][8])->toBe('1.300.000');
});

test('baris total laporan stock menjumlahkan semua unit dan mengabaikan harga kosong', function () {
    $this->actingAs(User::factory()->create(['status' => 1]));

    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker',
    ]);

    $firstStockIn = StockIn::create([
        'barang_id' => $barang->id,
        'jumlah' => 2,
        'tanggal_masuk' => now()->toDateString(),
        'harga_beli' => 1000000,
        'price_list' => 1300000,
        'toko_id' => $toko->id,
    ]);

    $secondStockIn = StockIn::create([
        'barang_id' => $barang->id,
        'jumlah' => 1,
        'tanggal_masuk' => now()->toDateString(),
        'harga_beli' => 500000,
        'price_list' => 750000,
        'toko_id' => $toko->id,
    ]);

    foreach ([$firstStockIn, $firstStockIn, $secondStockIn] as $index => $stockIn) {
        $unit = GudangBarang::create([
            'barang_id' => $barang->id,
            'serial_number_id' => 'SN-'.($index + 1),
            'toko_id' => $toko->id,
            'status' => 1,
        ]);

        DetailBarangMasuk::create([
            'stock_in_id' => $stockIn->id,
            'gudang_barang_id' => $unit->id,
        ]);
    }

    GudangBarang::create([
        'barang_id' => $barang->id,
        'serial_number_id' => 'SN-TANPA-HARGA',
        'toko_id' => $toko->id,
        'status' => 1,
    ]);

    $report = app(ReportController::class)->stock([]);

    $totalRow = $report['rows'][count($report['rows']) - 1];

    expect($report['rows'])->toHaveCount(5)
        ->and($totalRow[1])->toBe('TOTAL')
        ->and($totalRow[7])->toBe('2.500.000')
        ->and($totalRow[8])->toBe('3.350.000');
});

test('tabel laporan stock dipaginasi sehingga hanya satu halaman yang dimuat', function () {
    $this->actingAs(User::factory()->create(['status' => 1]));

    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker',
    ]);

    foreach (range(1, 30) as $i) {
        GudangBarang::create([
            'barang_id' => $barang->id,
            'serial_number_id' => 'SN-'.$i,
            'toko_id' => $toko->id,
            'status' => 1,
        ]);
    }

    $records = Livewire::test(StockReport::class)->instance()->getTableRecords();

    expect($records)->toBeInstanceOf(LengthAwarePaginator::class)
        ->and($records->total())->toBe(31)
        ->and($records->count())->toBe(25)
        ->and($records->perPage())->toBe(25);
});
