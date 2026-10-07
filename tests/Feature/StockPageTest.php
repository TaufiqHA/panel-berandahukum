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

test('stock page lists each unit with serial number, barang masuk id and tanggal masuk', function () {
    [$unit, $stockIn] = createStockUnit('Speaker', 'Merk A');

    Livewire::test(Stock::class)
        ->assertCanSeeTableRecords([$unit])
        ->assertTableColumnStateSet('barang.nama_product', 'Speaker', record: $unit)
        ->assertTableColumnStateSet('serial_number_id', 'SN-Speaker', record: $unit)
        ->assertTableColumnStateSet('detail_barang_masuk.stock_in.id', $stockIn->id, record: $unit)
        ->assertTableColumnStateSet('detail_barang_masuk.stock_in.tanggal_masuk', '2026-01-15', record: $unit);
});

test('stock page shows one row per serial number', function () {
    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker Bose DM5SE',
    ]);

    $units = [];
    foreach (['SN-1', 'SN-2', 'SN-3'] as $serial) {
        $units[] = GudangBarang::create([
            'barang_id' => $barang->id,
            'serial_number_id' => $serial,
            'toko_id' => $toko->id,
            'status' => 1,
        ]);
    }

    Livewire::test(Stock::class)
        ->assertCountTableRecords(3)
        ->assertCanSeeTableRecords($units)
        ->assertTableColumnStateSet('serial_number_id', 'SN-1', record: $units[0])
        ->assertTableColumnStateSet('serial_number_id', 'SN-3', record: $units[2]);
});

test('stock page shows the barang masuk id of each unit', function () {
    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker Bose DM5SE',
    ]);

    $stockInA = StockIn::create([
        'barang_id' => $barang->id,
        'jumlah' => 1,
        'tanggal_masuk' => '2026-01-10',
        'toko_id' => $toko->id,
    ]);
    $stockInB = StockIn::create([
        'barang_id' => $barang->id,
        'jumlah' => 1,
        'tanggal_masuk' => '2026-02-10',
        'toko_id' => $toko->id,
    ]);

    $first = GudangBarang::create([
        'barang_id' => $barang->id,
        'serial_number_id' => 'SN-MASUK-1',
        'toko_id' => $toko->id,
        'status' => 1,
    ]);
    DetailBarangMasuk::create([
        'stock_in_id' => $stockInA->id,
        'gudang_barang_id' => $first->id,
    ]);

    $second = GudangBarang::create([
        'barang_id' => $barang->id,
        'serial_number_id' => 'SN-MASUK-2',
        'toko_id' => $toko->id,
        'status' => 1,
    ]);
    DetailBarangMasuk::create([
        'stock_in_id' => $stockInB->id,
        'gudang_barang_id' => $second->id,
    ]);

    Livewire::test(Stock::class)
        ->assertTableColumnStateSet('detail_barang_masuk.stock_in.id', $stockInA->id, record: $first)
        ->assertTableColumnStateSet('detail_barang_masuk.stock_in.id', $stockInB->id, record: $second);
});

test('stock page excludes sold units', function () {
    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker Bose DM5SE',
    ]);

    $available = GudangBarang::create([
        'barang_id' => $barang->id,
        'serial_number_id' => 'SN-AVAILABLE',
        'toko_id' => $toko->id,
        'status' => 1,
    ]);
    $sold = GudangBarang::create([
        'barang_id' => $barang->id,
        'serial_number_id' => 'SN-SOLD',
        'toko_id' => $toko->id,
        'status' => 1,
    ]);
    $sold->delete();

    Livewire::test(Stock::class)
        ->assertCanSeeTableRecords([$available])
        ->assertCanNotSeeTableRecords([$sold]);
});

test('stock page shows one stock per serial number', function () {
    $toko = Toko::create(['nama_toko' => 'Toko A']);
    $kategori = Kategori::create(['nama_kategori' => 'Kategori A']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker',
        'merk' => 'Merk A',
    ]);

    $availableOne = GudangBarang::create([
        'barang_id' => $barang->id,
        'serial_number_id' => 'SN-1',
        'toko_id' => $toko->id,
        'status' => 1,
    ]);
    GudangBarang::create([
        'barang_id' => $barang->id,
        'serial_number_id' => 'SN-2',
        'toko_id' => $toko->id,
        'status' => 1,
    ]);

    Livewire::test(Stock::class)
        ->assertCanSeeTableRecords([$availableOne])
        ->assertTableColumnStateSet('stok', 1, record: $availableOne);
});

test('stock page filters units by available stock quantity', function () {
    [$unitWithStock] = createStockUnit('Speaker', 'Merk A', null, 1);
    [$unitWithoutStock] = createStockUnit('Amplifier', 'Merk B', null, 3);

    Livewire::test(Stock::class)
        ->filterTable('stok', 'eq0')
        ->assertCanSeeTableRecords([$unitWithoutStock])
        ->assertCanNotSeeTableRecords([$unitWithStock])
        ->resetTableFilters()
        ->filterTable('stok', '1-5')
        ->assertCanSeeTableRecords([$unitWithStock])
        ->assertCanNotSeeTableRecords([$unitWithoutStock]);
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

test('stock page filters units by toko', function () {
    $tokoA = Toko::create(['nama_toko' => 'Toko A']);
    $tokoB = Toko::create(['nama_toko' => 'Toko B']);

    [$unitA] = createStockUnit('Speaker', 'Merk A', $tokoA);
    [$unitB] = createStockUnit('Amplifier', 'Merk B', $tokoB);

    Livewire::test(Stock::class)
        ->filterTable('toko_id', $tokoA->id)
        ->assertCanSeeTableRecords([$unitA])
        ->assertCanNotSeeTableRecords([$unitB]);
});
