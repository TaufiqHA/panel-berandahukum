<?php

use App\Filament\Pages\Stock;
use App\Models\Barang;
use App\Models\DetailBarangMasuk;
use App\Models\GudangBarang;
use App\Models\Kategori;
use App\Models\StockIn;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['status' => 1]));
});

/**
 * @return array{0: GudangBarang, 1: StockIn}
 */
function createStockUnit(string $namaProduct, string $merk, ?Toko $toko = null, int $status = 1): array
{
    $toko ??= Toko::create(['nama_toko' => 'Toko '.$namaProduct]);
    $kategori = Kategori::create(['nama_kategori' => 'Kategori '.$namaProduct]);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => $namaProduct,
        'merk' => $merk,
        'warna' => 'Hitam',
        'satuan' => 'Pcs',
        'ukuran' => 'M',
    ]);

    $stockIn = StockIn::create([
        'barang_id' => $barang->id,
        'jumlah' => 1,
        'tanggal_masuk' => '2026-01-15',
        'toko_id' => $toko->id,
    ]);

    $unit = GudangBarang::create([
        'barang_id' => $barang->id,
        'serial_number_id' => 'SN-'.$namaProduct,
        'toko_id' => $toko->id,
        'status' => $status,
    ]);

    DetailBarangMasuk::create([
        'stock_in_id' => $stockIn->id,
        'gudang_barang_id' => $unit->id,
    ]);

    return [$unit, $stockIn];
}

test('stock page lists unit stock with barang masuk id and tanggal masuk', function () {
    [$unit, $stockIn] = createStockUnit('Speaker', 'Merk A');

    Livewire::test(Stock::class)
        ->assertCanSeeTableRecords([$unit])
        ->assertTableColumnStateSet('barang.nama_product', 'Speaker', record: $unit)
        ->assertTableColumnStateSet('detail_barang_masuk.stock_in.id', $stockIn->id, record: $unit);
});

test('stock page filters units by merk', function () {
    [$unitA] = createStockUnit('Speaker', 'Merk A');
    [$unitB] = createStockUnit('Amplifier', 'Merk B');

    Livewire::test(Stock::class)
        ->filterTable('merk', 'Merk A')
        ->assertCanSeeTableRecords([$unitA])
        ->assertCanNotSeeTableRecords([$unitB]);
});

test('stock page filters units by kategori', function () {
    [$unitA] = createStockUnit('Speaker', 'Merk A');
    [$unitB] = createStockUnit('Amplifier', 'Merk B');

    $kategoriId = $unitA->barang->kategori_id;

    Livewire::test(Stock::class)
        ->filterTable('kategori', $kategoriId)
        ->assertCanSeeTableRecords([$unitA])
        ->assertCanNotSeeTableRecords([$unitB]);
});

test('stock page filters units by toko and status', function () {
    $tokoA = Toko::create(['nama_toko' => 'Toko A']);
    $tokoB = Toko::create(['nama_toko' => 'Toko B']);

    [$unitA] = createStockUnit('Speaker', 'Merk A', $tokoA, 1);
    [$unitB] = createStockUnit('Amplifier', 'Merk B', $tokoB, 3);

    Livewire::test(Stock::class)
        ->filterTable('toko_id', $tokoA->id)
        ->assertCanSeeTableRecords([$unitA])
        ->assertCanNotSeeTableRecords([$unitB])
        ->resetTableFilters()
        ->filterTable('status', 3)
        ->assertCanSeeTableRecords([$unitB])
        ->assertCanNotSeeTableRecords([$unitA]);
});
