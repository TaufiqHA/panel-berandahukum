<?php

use App\Models\Barang;
use App\Models\DetailBarangMasuk;
use App\Models\DetailPenjualan;
use App\Models\GudangBarang;
use App\Models\Kategori;
use App\Models\Penjualan;
use App\Models\StockIn;
use App\Models\Toko;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function runDuplicateStockUnitRepairMigration(): void
{
    $migration = require database_path('migrations/2026_10_09_180000_remove_duplicate_available_and_sold_stock_units.php');

    $migration->up();
}

/**
 * @return array{barang: Barang, toko: Toko}
 */
function createDuplicateContext(): array
{
    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker Bose DS 40SE',
    ]);

    return ['barang' => $barang, 'toko' => $toko];
}

/**
 * @return array{unit: GudangBarang, stockIn: StockIn}
 */
function createDupUnit(Barang $barang, Toko $toko, string $serial, string $stockInEnteredAt): array
{
    $stockIn = StockIn::create([
        'barang_id' => $barang->id,
        'jumlah' => 1,
        'tanggal_masuk' => substr($stockInEnteredAt, 0, 10),
        'toko_id' => $toko->id,
    ]);
    DB::table('stock_ins')->where('id', $stockIn->id)->update([
        'created_at' => $stockInEnteredAt,
        'updated_at' => $stockInEnteredAt,
    ]);

    $unit = GudangBarang::create([
        'barang_id' => $barang->id,
        'serial_number_id' => $serial,
        'toko_id' => $toko->id,
        'status' => 1,
    ]);

    DetailBarangMasuk::create([
        'stock_in_id' => $stockIn->id,
        'gudang_barang_id' => $unit->id,
    ]);

    return ['unit' => $unit, 'stockIn' => $stockIn];
}

function markDupUnitSold(GudangBarang $unit, Toko $toko, string $kode, string $soldAt): DetailPenjualan
{
    $penjualan = Penjualan::create([
        'date' => substr($soldAt, 0, 10),
        'kode_penjualan' => $kode,
        'nama_pembeli' => 'Pembeli',
        'toko_id' => $toko->id,
        'subtotal' => 1000000,
        'status' => 1,
        'ppn' => 0,
        'show_infopembayaran' => 0,
    ]);
    DB::table('penjualans')->where('id', $penjualan->id)->update([
        'created_at' => $soldAt,
        'updated_at' => $soldAt,
    ]);

    $detail = DetailPenjualan::create([
        'penjualan_id' => $penjualan->id,
        'barang_id' => $unit->barang_id,
        'gudang_barang_id' => $unit->id,
        'price' => 1000000,
        'discount' => 0,
    ]);

    $unit->delete();

    return $detail;
}

test('it keeps the original stock-in when the duplicate was sold later', function () {
    ['barang' => $barang, 'toko' => $toko] = createDuplicateContext();

    ['unit' => $original, 'stockIn' => $originalStockIn] = createDupUnit($barang, $toko, 'SN-A', '2026-08-13 10:00:00');
    ['unit' => $duplicate, 'stockIn' => $duplicateStockIn] = createDupUnit($barang, $toko, 'SN-A', '2026-08-27 10:00:00');
    $detail = markDupUnitSold($duplicate, $toko, 'PJ-1', '2026-08-27 16:00:00');

    runDuplicateStockUnitRepairMigration();

    expect((int) $detail->fresh()->gudang_barang_id)->toBe($original->id)
        ->and(GudangBarang::withTrashed()->find($original->id)->deleted_at)->not->toBeNull()
        ->and(DB::table('gudang_barangs')->where('id', $duplicate->id)->exists())->toBeFalse()
        ->and(DB::table('stock_ins')->where('id', $originalStockIn->id)->exists())->toBeTrue()
        ->and(DB::table('stock_ins')->where('id', $duplicateStockIn->id)->exists())->toBeFalse();
});

test('it removes the phantom available duplicate when the original was sold', function () {
    ['barang' => $barang, 'toko' => $toko] = createDuplicateContext();

    ['unit' => $original, 'stockIn' => $originalStockIn] = createDupUnit($barang, $toko, 'SN-B', '2021-06-21 10:00:00');
    $detail = markDupUnitSold($original, $toko, 'PJ-2', '2021-08-13 09:16:00');
    ['unit' => $duplicate, 'stockIn' => $duplicateStockIn] = createDupUnit($barang, $toko, 'SN-B', '2021-08-12 10:00:00');

    runDuplicateStockUnitRepairMigration();

    expect((int) $detail->fresh()->gudang_barang_id)->toBe($original->id)
        ->and(DB::table('gudang_barangs')->where('id', $duplicate->id)->exists())->toBeFalse()
        ->and(DB::table('stock_ins')->where('id', $originalStockIn->id)->exists())->toBeTrue()
        ->and(DB::table('stock_ins')->where('id', $duplicateStockIn->id)->exists())->toBeFalse();
});

test('it matches serial numbers ignoring letter case', function () {
    ['barang' => $barang, 'toko' => $toko] = createDuplicateContext();

    ['unit' => $original] = createDupUnit($barang, $toko, '053542Z72820177AE', '2021-06-21 10:00:00');
    $detail = markDupUnitSold($original, $toko, 'PJ-3', '2021-08-13 09:16:00');
    ['unit' => $duplicate] = createDupUnit($barang, $toko, '053542z72820177AE', '2021-08-12 10:00:00');

    runDuplicateStockUnitRepairMigration();

    expect((int) $detail->fresh()->gudang_barang_id)->toBe($original->id)
        ->and(DB::table('gudang_barangs')->where('id', $duplicate->id)->exists())->toBeFalse();
});

test('it leaves serials without an available duplicate untouched', function () {
    ['barang' => $barang, 'toko' => $toko] = createDuplicateContext();

    ['unit' => $first] = createDupUnit($barang, $toko, 'SN-C', '2021-01-01 10:00:00');
    markDupUnitSold($first, $toko, 'PJ-4', '2021-02-01 10:00:00');
    ['unit' => $second] = createDupUnit($barang, $toko, 'SN-C', '2021-03-01 10:00:00');
    markDupUnitSold($second, $toko, 'PJ-5', '2021-04-01 10:00:00');

    runDuplicateStockUnitRepairMigration();

    expect(DB::table('gudang_barangs')->where('id', $first->id)->exists())->toBeTrue()
        ->and(DB::table('gudang_barangs')->where('id', $second->id)->exists())->toBeTrue();
});
