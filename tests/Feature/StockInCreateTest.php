<?php

use App\Filament\Resources\StockIns\Pages\CreateStockIn;
use App\Filament\Resources\StockIns\Pages\EditStockIn;
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

test('serial number tetap tampil di edit barang masuk walau unitnya sudah terjual', function () {
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
            'serial_items' => [
                ['serial_number' => 'SN-001'],
                ['serial_number' => 'SN-002'],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    // Unit SN-001 terjual sehingga gudang_barang-nya di-soft delete.
    GudangBarang::where('serial_number_id', 'SN-001')->firstOrFail()->delete();

    $stockIn = StockIn::firstOrFail();

    Livewire::test(EditStockIn::class, ['record' => $stockIn->getRouteKey()])
        ->assertFormSet(function (array $state): array {
            $serials = collect($state['serial_items'] ?? [])->pluck('serial_number')->all();

            expect($serials)->toContain('SN-001');
            expect($serials)->toContain('SN-002');

            return [];
        });
});

test('price list barang masuk diambil dari price list master barang saat dibuat', function () {
    $this->actingAs(User::factory()->create(['status' => 1]));

    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker',
        'harga' => 8807850,
    ]);

    Livewire::test(CreateStockIn::class)
        ->fillForm([
            'barang_id' => $barang->id,
            'jumlah' => 1,
            'tanggal_masuk' => now()->toDateString(),
            'toko_id' => $toko->id,
            'type_serial_number' => 2,
            'harga_beli' => 1385000,
            'serial_items' => [
                ['serial_number' => 'SN-001'],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect((float) StockIn::first()->price_list)->toBe(8807850.0);
});

test('price list barang masuk mengikuti price list master barang saat diubah', function () {
    $this->actingAs(User::factory()->create(['status' => 1]));

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
        'toko_id' => $toko->id,
        'price_list' => 1,
    ]);

    Livewire::test(EditStockIn::class, ['record' => $stockIn->getRouteKey()])
        ->assertFormSet(['price_list' => '8.807.850'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect((float) $stockIn->refresh()->price_list)->toBe(8807850.0);
});
