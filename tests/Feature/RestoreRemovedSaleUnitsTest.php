<?php

use App\Models\Barang;
use App\Models\DetailPenjualan;
use App\Models\GudangBarang;
use App\Models\Kategori;
use App\Models\Penjualan;
use App\Models\Toko;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function runRestoreRemovedSaleUnitsMigration(): void
{
    $migration = require database_path('migrations/2026_10_09_134658_restore_units_removed_from_sales.php');

    $migration->up();
}

/**
 * @return array{penjualan: Penjualan, barang: Barang, toko: Toko}
 */
function createRepairSale(string $waktu = '2026-09-23 12:53:59'): array
{
    $toko = Toko::create(['nama_toko' => 'Melindastore Jakarta']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Mic Conference Main Control Unit Broadway SG2100',
    ]);

    $penjualan = Penjualan::create([
        'date' => '2026-09-23',
        'kode_penjualan' => 'PJ-1',
        'nama_pembeli' => 'BP2TL',
        'toko_id' => $toko->id,
        'subtotal' => 1000000,
        'status' => 1,
        'ppn' => 0,
        'show_infopembayaran' => 0,
    ]);

    $penjualan->forceFill(['created_at' => $waktu, 'updated_at' => $waktu])->save();

    return ['penjualan' => $penjualan, 'barang' => $barang, 'toko' => $toko];
}

function createSoldUnit(int $barangId, int $tokoId, string $serialNumber, string $deletedAt): GudangBarang
{
    $unit = GudangBarang::create([
        'barang_id' => $barangId,
        'serial_number_id' => $serialNumber,
        'toko_id' => $tokoId,
        'status' => 1,
    ]);

    $unit->forceFill(['deleted_at' => $deletedAt])->save();

    return $unit;
}

test('it restores a unit whose sale line was soft deleted', function () {
    ['penjualan' => $penjualan, 'barang' => $barang, 'toko' => $toko] = createRepairSale();
    $unit = createSoldUnit($barang->id, $toko->id, '23120698', '2026-09-24 19:00:00');

    $detail = DetailPenjualan::create([
        'penjualan_id' => $penjualan->id,
        'barang_id' => $barang->id,
        'gudang_barang_id' => $unit->id,
        'price' => 1000000,
        'discount' => 0,
    ]);
    $detail->delete();

    runRestoreRemovedSaleUnitsMigration();

    $unit = GudangBarang::withTrashed()->find($unit->id);

    expect($unit->deleted_at)->toBeNull()
        ->and((int) $unit->status)->toBe(1);
});

test('it restores a unit whose legacy orphaned sale line was removed', function () {
    ['penjualan' => $penjualan, 'barang' => $barang, 'toko' => $toko] = createRepairSale();
    $unit = createSoldUnit($barang->id, $toko->id, '23120698', '2026-09-23 12:53:59');

    $detail = DetailPenjualan::create([
        'penjualan_id' => $penjualan->id,
        'barang_id' => $barang->id,
        'gudang_barang_id' => 0,
        'price' => 1000000,
        'discount' => 0,
    ]);
    $detail->delete();

    runRestoreRemovedSaleUnitsMigration();

    $unit = GudangBarang::withTrashed()->find($unit->id);

    expect($unit->deleted_at)->toBeNull()
        ->and((int) $unit->status)->toBe(1);
});

test('it keeps units that are still part of an active sale line', function () {
    ['penjualan' => $penjualan, 'barang' => $barang, 'toko' => $toko] = createRepairSale();
    $unit = createSoldUnit($barang->id, $toko->id, '23120699', '2026-09-23 12:53:59');

    DetailPenjualan::create([
        'penjualan_id' => $penjualan->id,
        'barang_id' => $barang->id,
        'gudang_barang_id' => $unit->id,
        'price' => 1000000,
        'discount' => 0,
    ]);

    runRestoreRemovedSaleUnitsMigration();

    $unit = GudangBarang::withTrashed()->find($unit->id);

    expect($unit->deleted_at)->not->toBeNull();
});

test('it keeps units without any sale evidence untouched', function () {
    ['barang' => $barang, 'toko' => $toko] = createRepairSale();
    $unit = createSoldUnit($barang->id, $toko->id, 'SN-ORPHAN', '2020-01-01 00:00:00');

    runRestoreRemovedSaleUnitsMigration();

    $unit = GudangBarang::withTrashed()->find($unit->id);

    expect($unit->deleted_at)->not->toBeNull();
});
