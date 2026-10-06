<?php

use App\Http\Controllers\PrintController;
use App\Models\Barang;
use App\Models\DetailPenjualan;
use App\Models\GudangBarang;
use App\Models\Kategori;
use App\Models\Penjualan;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * @return array{penjualan: Penjualan, barang: Barang, toko: Toko}
 */
function createPrintSale(): array
{
    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker Bose DM6C',
        'satuan' => 'psg',
        'wajib_serial_number' => true,
    ]);

    $penjualan = Penjualan::create([
        'date' => '2026-08-13',
        'kode_penjualan' => 'PJ - 0001544',
        'nama_pembeli' => 'PT Artha Prada',
        'toko_id' => $toko->id,
        'subtotal' => 1000000,
        'status' => 1,
        'ppn' => 0,
        'show_infopembayaran' => 0,
    ]);

    return ['penjualan' => $penjualan, 'barang' => $barang, 'toko' => $toko];
}

function createSaleUnit(Penjualan $penjualan, Barang $barang, Toko $toko, string $serialNumber, bool $trashed = false): DetailPenjualan
{
    $unit = GudangBarang::create([
        'barang_id' => $barang->id,
        'serial_number_id' => $serialNumber,
        'toko_id' => $toko->id,
        'status' => 1,
    ]);

    $detail = DetailPenjualan::create([
        'penjualan_id' => $penjualan->id,
        'barang_id' => $barang->id,
        'gudang_barang_id' => $unit->id,
        // Legacy bug: this column wrongly holds the stock unit id.
        'serial_number_id' => $unit->id,
        'price' => 17350000,
        'discount' => 30,
    ]);

    if ($trashed) {
        $detail->delete();
    }

    return $detail;
}

test('penjualan print quantity ignores soft deleted detail rows', function () {
    ['penjualan' => $penjualan, 'barang' => $barang, 'toko' => $toko] = createPrintSale();

    createSaleUnit($penjualan, $barang, $toko, 'SN-NOW');
    createSaleUnit($penjualan, $barang, $toko, 'SN-OLD-1', trashed: true);
    createSaleUnit($penjualan, $barang, $toko, 'SN-OLD-2', trashed: true);

    $lines = $penjualan->fresh()->barang_pembelian;

    expect($lines)->toHaveCount(1)
        ->and((int) $lines->first()->pivot->count)->toBe(1);
});

test('penjualan print resolves the serial number from the stock unit', function () {
    ['penjualan' => $penjualan, 'barang' => $barang, 'toko' => $toko] = createPrintSale();
    $detail = createSaleUnit($penjualan, $barang, $toko, 'SN-REAL-1');

    $controller = new PrintController;
    $method = new ReflectionMethod($controller, 'resolveSerialNumber');
    $method->setAccessible(true);

    expect($method->invoke($controller, $detail))->toBe('SN-REAL-1');
});

test('penjualan print attaches every unit serial number to the line', function () {
    ['penjualan' => $penjualan, 'barang' => $barang, 'toko' => $toko] = createPrintSale();
    createSaleUnit($penjualan, $barang, $toko, 'SN-REAL-1');
    createSaleUnit($penjualan, $barang, $toko, 'SN-REAL-2');

    $controller = new PrintController;
    $method = new ReflectionMethod($controller, 'attachSerialNumbers');
    $method->setAccessible(true);

    $penjualan = $penjualan->fresh()->load('barang_pembelian');
    $method->invoke($controller, $penjualan);

    expect($penjualan->barang_pembelian->first()->pivot->serial_number)
        ->toBe('SN-REAL-1, SN-REAL-2');
});

test('penjualan print renders a pdf', function () {
    $this->actingAs(User::factory()->create(['status' => 1]));
    ['penjualan' => $penjualan, 'barang' => $barang, 'toko' => $toko] = createPrintSale();
    createSaleUnit($penjualan, $barang, $toko, 'SN-REAL-1');

    $this->get(route('print.penjualan', $penjualan))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf');
});
