<?php

use App\Filament\Resources\StockIns\Pages\CreateStockIn;
use App\Models\Barang;
use App\Models\DetailBarangMasuk;
use App\Models\GudangBarang;
use App\Models\Kategori;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('creates a barang masuk with one-by-one serial numbers', function () {
    $this->actingAs(User::factory()->create(['status' => 1]));

    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker',
        'harga' => 1000000,
    ]);

    Livewire::test(CreateStockIn::class)
        ->fillForm([
            'barang_id' => $barang->id,
            'jumlah' => 2,
            'tanggal_masuk' => now()->toDateString(),
            'toko_id' => $toko->id,
            'type_serial_number' => 2,
            'harga_beli' => 800000,
            'harga_jual' => 1000000,
            'price_list' => 950000,
            'serial_items' => [
                ['serial_number' => 'SN-001'],
                ['serial_number' => 'SN-002'],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(GudangBarang::count())->toBe(2);
    expect(GudangBarang::pluck('serial_number_id')->all())->toBe(['SN-001', 'SN-002']);
    expect(DetailBarangMasuk::count())->toBe(2);
});
