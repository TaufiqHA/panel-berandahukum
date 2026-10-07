<?php

use App\Filament\Pages\SearchBarang;
use App\Models\Barang;
use App\Models\DetailBarangMasuk;
use App\Models\DetailPenjualan;
use App\Models\GudangBarang;
use App\Models\Kategori;
use App\Models\Penjualan;
use App\Models\StockIn;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['status' => 1, 'user_menu' => 'Search']));
});

/**
 * @return array{unit: GudangBarang, penjualan: Penjualan}
 */
function createSoldSearchUnit(): array
{
    $toko = Toko::create(['nama_toko' => 'Melindastore Jakarta']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker Bose DM5SE',
    ]);

    $unit = GudangBarang::create([
        'barang_id' => $barang->id,
        'serial_number_id' => 'SN-SOLD-1',
        'toko_id' => $toko->id,
        'status' => 1,
    ]);

    $stockIn = StockIn::create([
        'barang_id' => $barang->id,
        'jumlah' => 1,
        'tanggal_masuk' => '2026-09-14',
        'toko_id' => $toko->id,
    ]);

    $detailBarangMasuk = DetailBarangMasuk::create([
        'stock_in_id' => $stockIn->id,
        'gudang_barang_id' => $unit->id,
    ]);

    $penjualan = Penjualan::create([
        'date' => '2026-09-21',
        'kode_penjualan' => 'PJ-1',
        'nama_pembeli' => 'PT UG Mandiri',
        'toko_id' => $toko->id,
        'subtotal' => 13230000,
        'status' => 1,
        'ppn' => 0,
        'show_infopembayaran' => 0,
    ]);

    DetailPenjualan::create([
        'penjualan_id' => $penjualan->id,
        'barang_id' => $barang->id,
        'gudang_barang_id' => $unit->id,
        // Legacy bug: this column wrongly holds the stock unit id.
        'serial_number_id' => $unit->id,
        'price' => 13230000,
        'discount' => 0,
    ]);

    // Selling the unit soft deletes it from stock, and the stock-in records it
    // originated from may have been soft deleted as well.
    $unit->delete();
    $detailBarangMasuk->delete();
    $stockIn->delete();

    return ['unit' => $unit, 'penjualan' => $penjualan];
}

test('search page lists sold units with buyer, sale date and entry date', function () {
    ['unit' => $unit] = createSoldSearchUnit();

    Livewire::test(SearchBarang::class)
        ->assertCanSeeTableRecords([$unit])
        ->assertTableColumnStateSet('serial_number_id', 'SN-SOLD-1', record: $unit)
        ->assertTableColumnStateSet('detail_penjualan.penjualan.kode_penjualan', 'PJ-1', record: $unit)
        ->assertTableColumnStateSet('detail_penjualan.penjualan.nama_pembeli', 'PT UG Mandiri', record: $unit)
        ->assertTableColumnStateSet('detail_penjualan.penjualan.date', '2026-09-21', record: $unit)
        ->assertTableColumnStateSet('detail_barang_masuk.stock_in.tanggal_masuk', '2026-09-14', record: $unit);
});

test('search page finds a sold unit by serial number', function () {
    ['unit' => $unit] = createSoldSearchUnit();

    Livewire::test(SearchBarang::class)
        ->searchTable('SN-SOLD-1')
        ->assertCanSeeTableRecords([$unit]);
});

test('search page still lists units that are in stock', function () {
    ['unit' => $sold] = createSoldSearchUnit();

    $stock = GudangBarang::create([
        'barang_id' => $sold->barang_id,
        'serial_number_id' => 'SN-STOCK-1',
        'toko_id' => $sold->toko_id,
        'status' => 1,
    ]);

    Livewire::test(SearchBarang::class)
        ->assertCanSeeTableRecords([$sold, $stock]);
});

test('search page filters sold and stock units separately', function () {
    ['unit' => $sold] = createSoldSearchUnit();

    $stock = GudangBarang::create([
        'barang_id' => $sold->barang_id,
        'serial_number_id' => 'SN-STOCK-1',
        'toko_id' => $sold->toko_id,
        'status' => 1,
    ]);

    Livewire::test(SearchBarang::class)
        ->filterTable('status', 'stock')
        ->assertCanSeeTableRecords([$stock])
        ->assertCanNotSeeTableRecords([$sold])
        ->resetTableFilters()
        ->filterTable('status', 'terjual')
        ->assertCanSeeTableRecords([$sold])
        ->assertCanNotSeeTableRecords([$stock]);
});
