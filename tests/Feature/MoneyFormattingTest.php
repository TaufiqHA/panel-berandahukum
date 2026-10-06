<?php

use App\Filament\Resources\Barangs\Pages\EditBarang;
use App\Filament\Resources\StockIns\Pages\ListStockIns;
use App\Models\Barang;
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

test('field harga di master barang menampilkan titik ribuan', function () {
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker',
        'harga' => 1385000,
    ]);

    Livewire::test(EditBarang::class, ['record' => $barang->getRouteKey()])
        ->assertFormSet(['harga' => '1.385.000']);
});

test('harga yang diisi dengan titik ribuan tersimpan sebagai angka', function () {
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker',
        'harga' => 1000000,
    ]);

    Livewire::test(EditBarang::class, ['record' => $barang->getRouteKey()])
        ->fillForm(['harga' => '8.807.850'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect((float) $barang->refresh()->harga)->toBe(8807850.0);
});

test('tabel barang masuk menampilkan harga beli dan price list dengan titik ribuan', function () {
    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker',
        'harga' => 8807850,
    ]);

    $stockIn = StockIn::create([
        'barang_id' => $barang->id,
        'jumlah' => 1,
        'tanggal_masuk' => now()->toDateString(),
        'harga_beli' => 1385000,
        'price_list' => 8807850,
        'toko_id' => $toko->id,
    ]);

    Livewire::test(ListStockIns::class)
        ->assertTableColumnFormattedStateSet('harga_beli', "Rp\u{A0}1.385.000", $stockIn)
        ->assertTableColumnFormattedStateSet('price_list', "Rp\u{A0}8.807.850", $stockIn);
});
