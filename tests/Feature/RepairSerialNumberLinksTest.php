<?php

use App\Models\Barang;
use App\Models\DetailPenjualan;
use App\Models\Kategori;
use App\Models\Penjualan;
use App\Models\Toko;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function runRepairSerialNumberLinksMigration(): void
{
    $migration = require database_path('migrations/2026_10_06_160855_repair_missing_serial_number_links_on_detail_penjualans.php');

    $migration->up();
}

/**
 * @return array{penjualan: Penjualan, barang: Barang, toko: Toko}
 */
function createOrphanedSale(string $waktu = '2026-07-08 16:21:35'): array
{
    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker Bose',
    ]);

    $penjualan = Penjualan::create([
        'date' => '2026-07-08',
        'kode_penjualan' => 'PJ-1',
        'nama_pembeli' => 'Pembeli',
        'toko_id' => $toko->id,
        'subtotal' => 1000000,
        'status' => 1,
        'ppn' => 0,
        'show_infopembayaran' => 0,
    ]);

    DB::table('penjualans')->where('id', $penjualan->id)->update([
        'created_at' => $waktu,
        'updated_at' => $waktu,
    ]);

    return ['penjualan' => $penjualan, 'barang' => $barang, 'toko' => $toko];
}

function createSoldStockUnit(int $barangId, int $tokoId, ?string $serialNumber, string $deletedAt): int
{
    return DB::table('gudang_barangs')->insertGetId([
        'barang_id' => $barangId,
        'serial_number_id' => $serialNumber,
        'toko_id' => $tokoId,
        'status' => 1,
        'created_at' => $deletedAt,
        'updated_at' => $deletedAt,
        'deleted_at' => $deletedAt,
    ]);
}

test('repair migration relinks an orphaned detail row to its deleted stock unit', function () {
    ['penjualan' => $penjualan, 'barang' => $barang, 'toko' => $toko] = createOrphanedSale();
    $unitId = createSoldStockUnit($barang->id, $toko->id, 'SN-ABC-123', '2026-07-08 16:21:35');

    $detail = DetailPenjualan::create([
        'penjualan_id' => $penjualan->id,
        'barang_id' => $barang->id,
        'gudang_barang_id' => 0,
        'price' => 1000000,
        'discount' => 0,
    ]);

    runRepairSerialNumberLinksMigration();

    $detail->refresh();
    expect($detail->gudang_barang_id)->toBe($unitId)
        ->and($detail->serial_number_id)->toBeNull();
});

test('repair migration stores a numeric serial number as integer', function () {
    ['penjualan' => $penjualan, 'barang' => $barang, 'toko' => $toko] = createOrphanedSale();
    $unitId = createSoldStockUnit($barang->id, $toko->id, '23120623', '2026-07-08 16:21:35');

    $detail = DetailPenjualan::create([
        'penjualan_id' => $penjualan->id,
        'barang_id' => $barang->id,
        'gudang_barang_id' => 0,
        'price' => 1000000,
        'discount' => 0,
    ]);

    runRepairSerialNumberLinksMigration();

    $detail->refresh();
    expect($detail->gudang_barang_id)->toBe($unitId)
        ->and($detail->serial_number_id)->toBe(23120623);
});

test('repair migration leaves an orphaned detail row untouched when the unit count does not match', function () {
    ['penjualan' => $penjualan, 'barang' => $barang, 'toko' => $toko] = createOrphanedSale();
    createSoldStockUnit($barang->id, $toko->id, 'SN-1', '2026-07-08 16:21:35');
    createSoldStockUnit($barang->id, $toko->id, 'SN-2', '2026-07-08 16:21:35');

    $detail = DetailPenjualan::create([
        'penjualan_id' => $penjualan->id,
        'barang_id' => $barang->id,
        'gudang_barang_id' => 0,
        'price' => 1000000,
        'discount' => 0,
    ]);

    runRepairSerialNumberLinksMigration();

    expect($detail->fresh()->gudang_barang_id)->toBe(0);
});
