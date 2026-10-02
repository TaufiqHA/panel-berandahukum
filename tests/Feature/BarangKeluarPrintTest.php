<?php

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\DataBarangKeluar;
use App\Models\Kategori;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['status' => 1]));

    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker',
        'satuan' => 'bh',
        'harga' => 1000000,
    ]);

    $this->barangKeluar = BarangKeluar::create([
        'date' => now()->toDateString(),
        'kode_barang_keluar' => 'BK - 0000001',
        'nama_penerima' => 'Penerima Test',
        'alamat_penerima' => 'Jl. Test',
        'telepon_penerima' => '08123',
        'toko_id' => $toko->id,
        'nama_sales' => 'Sales Test',
        'status' => 1,
    ]);

    DataBarangKeluar::create([
        'barang_keluar_id' => $this->barangKeluar->id,
        'barang_id' => $barang->id,
    ]);
});

test('barang keluar print renders a pdf', function () {
    $this->get(route('print.barang-keluar', $this->barangKeluar))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf');
});

test('barang keluar surat jalan renders a pdf', function () {
    $this->get(route('print.barang-keluar.surat-jalan', $this->barangKeluar))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf');
});
