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

function runDuplicatePagiSoreStockInMigration(): void
{
    $migration = require database_path('migrations/2026_10_09_170100_remove_duplicate_pagi_sore_dm6se_stock_in.php');

    $migration->up();
}

/**
 * @return array{
 *     barang: Barang,
 *     toko: Toko,
 *     penjualan: Penjualan,
 *     originalUnit: GudangBarang,
 *     originalStockIn: StockIn,
 *     duplicateUnit: GudangBarang,
 *     duplicateStockIn: StockIn,
 *     detail: DetailPenjualan
 * }
 */
function createDuplicatePagiSoreScenario(): array
{
    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker Bose DM6SE',
    ]);

    $originalStockIn = StockIn::create([
        'barang_id' => $barang->id,
        'jumlah' => 1,
        'tanggal_masuk' => '2026-06-16',
        'toko_id' => $toko->id,
    ]);
    $originalUnit = GudangBarang::create([
        'barang_id' => $barang->id,
        'serial_number_id' => '085506Z53480130BE',
        'toko_id' => $toko->id,
        'status' => 1,
    ]);
    DetailBarangMasuk::create([
        'stock_in_id' => $originalStockIn->id,
        'gudang_barang_id' => $originalUnit->id,
    ]);

    $duplicateStockIn = StockIn::create([
        'barang_id' => $barang->id,
        'jumlah' => 1,
        'tanggal_masuk' => '2026-06-16',
        'toko_id' => $toko->id,
    ]);
    $duplicateUnit = GudangBarang::create([
        'barang_id' => $barang->id,
        'serial_number_id' => '085506Z53480130BE',
        'toko_id' => $toko->id,
        'status' => 1,
    ]);
    DetailBarangMasuk::create([
        'stock_in_id' => $duplicateStockIn->id,
        'gudang_barang_id' => $duplicateUnit->id,
    ]);

    $penjualan = Penjualan::create([
        'date' => '2026-07-27',
        'kode_penjualan' => 'PJ - 0001545',
        'nama_pembeli' => 'RM Pagi Sore Gatsu',
        'toko_id' => $toko->id,
        'subtotal' => 22000000,
        'status' => 1,
        'ppn' => 0,
        'show_infopembayaran' => 0,
    ]);

    $detail = DetailPenjualan::create([
        'penjualan_id' => $penjualan->id,
        'barang_id' => $barang->id,
        'gudang_barang_id' => $duplicateUnit->id,
        'price' => 22000000,
        'discount' => 0,
    ]);

    // The re-entered unit is marked as sold.
    $duplicateUnit->delete();

    return compact(
        'barang',
        'toko',
        'penjualan',
        'originalUnit',
        'originalStockIn',
        'duplicateUnit',
        'duplicateStockIn',
        'detail',
    );
}

test('it reconnects the active sale to the original stock unit', function () {
    ['originalUnit' => $originalUnit, 'detail' => $detail] = createDuplicatePagiSoreScenario();

    runDuplicatePagiSoreStockInMigration();

    expect((int) $detail->fresh()->gudang_barang_id)->toBe($originalUnit->id);
});

test('it marks the original unit as sold', function () {
    ['originalUnit' => $originalUnit, 'penjualan' => $penjualan] = createDuplicatePagiSoreScenario();

    runDuplicatePagiSoreStockInMigration();

    $originalUnit = GudangBarang::withTrashed()->find($originalUnit->id);

    expect($originalUnit->deleted_at)->not->toBeNull()
        ->and($originalUnit->deleted_at->equalTo($penjualan->created_at))->toBeTrue();
});

test('it removes the duplicate stock-in and the re-entered unit', function () {
    ['duplicateUnit' => $duplicateUnit, 'duplicateStockIn' => $duplicateStockIn] = createDuplicatePagiSoreScenario();

    runDuplicatePagiSoreStockInMigration();

    expect(DB::table('gudang_barangs')->where('id', $duplicateUnit->id)->exists())->toBeFalse()
        ->and(DB::table('stock_ins')->where('id', $duplicateStockIn->id)->exists())->toBeFalse()
        ->and(DB::table('detail_barang_masuks')->where('gudang_barang_id', $duplicateUnit->id)->exists())->toBeFalse();
});

test('it keeps the original stock-in record', function () {
    ['originalStockIn' => $originalStockIn] = createDuplicatePagiSoreScenario();

    runDuplicatePagiSoreStockInMigration();

    expect(DB::table('stock_ins')->where('id', $originalStockIn->id)->exists())->toBeTrue();
});

test('it leaves sales without duplicates untouched', function () {
    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker Bose DM6SE',
    ]);

    $unit = GudangBarang::create([
        'barang_id' => $barang->id,
        'serial_number_id' => 'SN-UNIQUE',
        'toko_id' => $toko->id,
        'status' => 1,
    ]);

    $penjualan = Penjualan::create([
        'date' => '2026-07-27',
        'kode_penjualan' => 'PJ - 0001545',
        'nama_pembeli' => 'RM Pagi Sore Gatsu',
        'toko_id' => $toko->id,
        'subtotal' => 22000000,
        'status' => 1,
        'ppn' => 0,
        'show_infopembayaran' => 0,
    ]);

    $detail = DetailPenjualan::create([
        'penjualan_id' => $penjualan->id,
        'barang_id' => $barang->id,
        'gudang_barang_id' => $unit->id,
        'price' => 22000000,
        'discount' => 0,
    ]);

    runDuplicatePagiSoreStockInMigration();

    expect((int) $detail->fresh()->gudang_barang_id)->toBe($unit->id)
        ->and(DB::table('gudang_barangs')->where('id', $unit->id)->exists())->toBeTrue();
});
