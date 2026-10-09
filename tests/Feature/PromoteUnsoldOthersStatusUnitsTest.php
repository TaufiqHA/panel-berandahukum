<?php

use App\Models\Barang;
use App\Models\DetailPenjualan;
use App\Models\GudangBarang;
use App\Models\Kategori;
use App\Models\Penjualan;
use App\Models\Toko;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function runPromoteUnsoldOthersStatusUnitsMigration(): void
{
    $migration = require database_path('migrations/2026_10_09_170000_promote_unsold_others_status_units_to_stock.php');

    $migration->up();
}

/**
 * @return array{barang: Barang, toko: Toko, penjualan: Penjualan}
 */
function createOthersStatusContext(): array
{
    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker Bose DM6SE',
    ]);

    $penjualan = Penjualan::create([
        'date' => '2026-09-01',
        'kode_penjualan' => 'PJ-1',
        'nama_pembeli' => 'Pembeli',
        'toko_id' => $toko->id,
        'subtotal' => 1000000,
        'status' => 1,
        'ppn' => 0,
        'show_infopembayaran' => 0,
    ]);

    return ['barang' => $barang, 'toko' => $toko, 'penjualan' => $penjualan];
}

function createOthersStatusUnit(int $barangId, int $tokoId, string $serial): GudangBarang
{
    return GudangBarang::create([
        'barang_id' => $barangId,
        'serial_number_id' => $serial,
        'toko_id' => $tokoId,
        'status' => 2,
    ]);
}

test('it promotes an unsold lainnya unit to available stock', function () {
    ['barang' => $barang, 'toko' => $toko] = createOthersStatusContext();
    $unit = createOthersStatusUnit($barang->id, $toko->id, 'SN-ORPHAN');

    runPromoteUnsoldOthersStatusUnitsMigration();

    expect((int) $unit->fresh()->status)->toBe(1);
});

test('it keeps a lainnya unit that is still part of an active sale', function () {
    ['barang' => $barang, 'toko' => $toko, 'penjualan' => $penjualan] = createOthersStatusContext();
    $unit = createOthersStatusUnit($barang->id, $toko->id, 'SN-SOLD');

    DetailPenjualan::create([
        'penjualan_id' => $penjualan->id,
        'barang_id' => $barang->id,
        'gudang_barang_id' => $unit->id,
        'price' => 1000000,
        'discount' => 0,
    ]);

    runPromoteUnsoldOthersStatusUnitsMigration();

    expect((int) $unit->fresh()->status)->toBe(2);
});

test('it promotes a lainnya unit whose sale line was removed', function () {
    ['barang' => $barang, 'toko' => $toko, 'penjualan' => $penjualan] = createOthersStatusContext();
    $unit = createOthersStatusUnit($barang->id, $toko->id, 'SN-REMOVED');

    $detail = DetailPenjualan::create([
        'penjualan_id' => $penjualan->id,
        'barang_id' => $barang->id,
        'gudang_barang_id' => $unit->id,
        'price' => 1000000,
        'discount' => 0,
    ]);
    $detail->delete();

    runPromoteUnsoldOthersStatusUnitsMigration();

    expect((int) $unit->fresh()->status)->toBe(1);
});

test('it leaves available and deleted units untouched', function () {
    ['barang' => $barang, 'toko' => $toko] = createOthersStatusContext();

    $available = GudangBarang::create([
        'barang_id' => $barang->id,
        'serial_number_id' => 'SN-AVAILABLE',
        'toko_id' => $toko->id,
        'status' => 1,
    ]);

    $deleted = createOthersStatusUnit($barang->id, $toko->id, 'SN-DELETED');
    $deleted->delete();

    runPromoteUnsoldOthersStatusUnitsMigration();

    expect((int) $available->fresh()->status)->toBe(1)
        ->and(GudangBarang::withTrashed()->find($deleted->id)->status)->toBe(2);
});
