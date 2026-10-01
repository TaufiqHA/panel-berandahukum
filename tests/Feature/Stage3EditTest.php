<?php

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\PindahGudang;
use App\Models\StockIn;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['status' => 1]));
});

test('barang masuk edit page renders', function () {
    $toko = Toko::create(['nama_toko' => 'Toko A']);
    $kategori = Kategori::create(['nama_kategori' => 'Kategori A']);
    $barang = Barang::create(['kategori_id' => $kategori->id, 'nama_product' => 'Barang A']);
    $stockIn = StockIn::create([
        'barang_id' => $barang->id,
        'jumlah' => 1,
        'tanggal_masuk' => now()->toDateString(),
        'toko_id' => $toko->id,
        'type_serial_number' => 2,
    ]);

    $this->get("/admin/stock-ins/{$stockIn->id}/edit")->assertSuccessful();
});

test('pindah toko edit page renders', function () {
    $from = Toko::create(['nama_toko' => 'Toko A']);
    $to = Toko::create(['nama_toko' => 'Toko B']);
    $transfer = PindahGudang::create([
        'date' => now()->toDateString(),
        'no_ref' => 'REF-1',
        'from' => $from->id,
        'to' => $to->id,
        'status' => 1,
    ]);

    $this->get("/admin/pindah-gudangs/{$transfer->id}/edit")->assertSuccessful();
});
