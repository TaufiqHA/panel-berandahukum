<?php

use App\Filament\Resources\BarangKeluars\Pages\CreateBarangKeluar;
use App\Filament\Resources\Penjualans\Pages\CreatePenjualan;
use App\Filament\Resources\Penjualans\Pages\EditPenjualan;
use App\Filament\Resources\Pos\Pages\CreatePo;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\DataBarangKeluar;
use App\Models\DetailPenjualan;
use App\Models\GudangBarang;
use App\Models\Kategori;
use App\Models\Penjualan;
use App\Models\Po;
use App\Models\PoDetail;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['status' => 1]));

    $this->toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $this->barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker',
        'harga' => 1000000,
    ]);
});

test('creates a purchase order with line items', function () {
    Livewire::test(CreatePo::class)
        ->fillForm([
            'date' => now()->toDateString(),
            'kode_po' => 'PO-001',
            'toko_id' => $this->toko->id,
            'items' => [
                ['barang_id' => $this->barang->id, 'jumlah' => 2, 'price' => 1000000, 'discount' => 10],
                ['barang_id' => $this->barang->id, 'jumlah' => 1, 'price' => 500000, 'discount' => 0],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(PoDetail::count())->toBe(2);
    // 2 * 1.000.000 * 0.9 = 1.800.000 ; + 500.000 = 2.300.000
    expect((float) Po::first()->subtotal)->toBe(2300000.0);
});

test('creating a penjualan consumes stock units', function () {
    $unit = GudangBarang::create([
        'barang_id' => $this->barang->id,
        'serial_number_id' => 'SN-1',
        'toko_id' => $this->toko->id,
        'status' => 1,
    ]);

    Livewire::test(CreatePenjualan::class)
        ->fillForm([
            'date' => now()->toDateString(),
            'kode_penjualan' => 'PJ-001',
            'nama_pembeli' => 'Pembeli A',
            'toko_id' => $this->toko->id,
            'items' => [
                [
                    'barang_id' => $this->barang->id,
                    'jumlah' => 1,
                    'serial_numbers' => [$unit->id],
                    'price' => 1000000,
                    'discount' => 0,
                ],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(DetailPenjualan::count())->toBe(1);
    expect(GudangBarang::find($unit->id))->toBeNull();
    expect(GudangBarang::withTrashed()->find($unit->id))->not->toBeNull();
});

test('creating a barang keluar consumes stock units', function () {
    $unit = GudangBarang::create([
        'barang_id' => $this->barang->id,
        'serial_number_id' => 'SN-2',
        'toko_id' => $this->toko->id,
        'status' => 1,
    ]);

    Livewire::test(CreateBarangKeluar::class)
        ->fillForm([
            'date' => now()->toDateString(),
            'kode_barang_keluar' => 'BK-001',
            'toko_id' => $this->toko->id,
            'items' => [
                [
                    'barang_id' => $this->barang->id,
                    'jumlah' => 1,
                    'serial_numbers' => [$unit->id],
                ],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(DataBarangKeluar::count())->toBe(1);
    expect(GudangBarang::find($unit->id))->toBeNull();
});

test('penjualan edit fills the PPN toggle from the stored value', function () {
    $unit = GudangBarang::create([
        'barang_id' => $this->barang->id,
        'serial_number_id' => 'SN-PPN',
        'toko_id' => $this->toko->id,
        'status' => 1,
    ]);

    Livewire::test(CreatePenjualan::class)
        ->fillForm([
            'date' => now()->toDateString(),
            'kode_penjualan' => 'PJ-PPN',
            'nama_pembeli' => 'Pembeli PPN',
            'toko_id' => $this->toko->id,
            'use_ppn' => true,
            'items' => [
                ['barang_id' => $this->barang->id, 'jumlah' => 1, 'serial_numbers' => [$unit->id], 'price' => 1000000, 'discount' => 0],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $penjualan = Penjualan::first();
    expect((float) $penjualan->ppn)->toBeGreaterThan(0);

    Livewire::test(EditPenjualan::class, ['record' => $penjualan->getRouteKey()])
        ->assertFormSet(['use_ppn' => true]);
});

test('transaction edit pages render with their line items', function () {
    $unit = GudangBarang::create([
        'barang_id' => $this->barang->id,
        'serial_number_id' => 'SN-EDIT',
        'toko_id' => $this->toko->id,
        'status' => 1,
    ]);

    Livewire::test(CreatePenjualan::class)
        ->fillForm([
            'date' => now()->toDateString(),
            'kode_penjualan' => 'PJ-EDIT',
            'nama_pembeli' => 'Pembeli A',
            'toko_id' => $this->toko->id,
            'items' => [
                ['barang_id' => $this->barang->id, 'jumlah' => 1, 'serial_numbers' => [$unit->id], 'price' => 1000000, 'discount' => 0],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->get('/admin/penjualans/'.Penjualan::first()->id.'/edit')
        ->assertSuccessful()
        ->assertSee('SN-EDIT');

    $unit2 = GudangBarang::create([
        'barang_id' => $this->barang->id,
        'serial_number_id' => 'SN-EDIT-2',
        'toko_id' => $this->toko->id,
        'status' => 1,
    ]);

    Livewire::test(CreateBarangKeluar::class)
        ->fillForm([
            'date' => now()->toDateString(),
            'kode_barang_keluar' => 'BK-EDIT',
            'toko_id' => $this->toko->id,
            'items' => [
                ['barang_id' => $this->barang->id, 'jumlah' => 1, 'serial_numbers' => [$unit2->id]],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->get('/admin/barang-keluars/'.BarangKeluar::first()->id.'/edit')->assertSuccessful();
});
