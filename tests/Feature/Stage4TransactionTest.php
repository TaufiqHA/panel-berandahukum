<?php

use App\Filament\Resources\BarangKeluars\Pages\CreateBarangKeluar;
use App\Filament\Resources\Penjualans\Pages\CreatePenjualan;
use App\Filament\Resources\Penjualans\Pages\EditPenjualan;
use App\Filament\Resources\Pos\Pages\CreatePo;
use App\Filament\Resources\Pos\Pages\EditPo;
use App\Filament\Support\LineItems;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\DataBarangKeluar;
use App\Models\DetailPenjualan;
use App\Models\GudangBarang;
use App\Models\Kategori;
use App\Models\Penjualan;
use App\Models\Po;
use App\Models\PoDetail;
use App\Models\Signature;
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

test('removing an item from a penjualan returns the unit to available stock', function () {
    Signature::create(['name' => 'Sales Test', 'signature' => 'TTD']);

    $keep = GudangBarang::create([
        'barang_id' => $this->barang->id,
        'serial_number_id' => 'SN-KEEP',
        'toko_id' => $this->toko->id,
        'status' => 1,
    ]);

    $remove = GudangBarang::create([
        'barang_id' => $this->barang->id,
        'serial_number_id' => 'SN-REMOVE',
        'toko_id' => $this->toko->id,
        'status' => 1,
    ]);

    Livewire::test(CreatePenjualan::class)
        ->fillForm([
            'date' => now()->toDateString(),
            'kode_penjualan' => 'PJ-REMOVE',
            'nama_pembeli' => 'Pembeli Remove',
            'nama_sales' => 'Sales Test',
            'toko_id' => $this->toko->id,
            'items' => [
                ['barang_id' => $this->barang->id, 'jumlah' => 1, 'serial_numbers' => [$keep->id], 'price' => 1000000, 'discount' => 0],
                ['barang_id' => $this->barang->id, 'jumlah' => 1, 'serial_numbers' => [$remove->id], 'price' => 1000000, 'discount' => 0],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $penjualan = Penjualan::first();

    // Simulate a legacy sold unit that is not soft deleted.
    GudangBarang::withTrashed()->whereKey($remove->id)->update(['status' => 2, 'deleted_at' => null]);

    Livewire::test(EditPenjualan::class, ['record' => $penjualan->getRouteKey()])
        ->fillForm([
            'items' => [
                ['barang_id' => $this->barang->id, 'jumlah' => 1, 'serial_numbers' => [$keep->id], 'price' => 1000000, 'discount' => 0],
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $removedUnit = GudangBarang::withTrashed()->find($remove->id);

    expect($removedUnit->deleted_at)->toBeNull()
        ->and((int) $removedUnit->status)->toBe(1)
        ->and($removedUnit->resolvePenjualan())->toBeNull();

    // The kept unit is still consumed by the sale.
    expect(GudangBarang::find($keep->id))->toBeNull();
});

test('removing an imported sale line returns its detached unit to available stock', function () {
    $unit = GudangBarang::create([
        'barang_id' => $this->barang->id,
        'serial_number_id' => 'SN-ORPHAN',
        'toko_id' => $this->toko->id,
        'status' => 1,
    ]);

    $penjualan = Penjualan::create([
        'date' => now()->toDateString(),
        'kode_penjualan' => 'PJ-ORPHAN',
        'nama_pembeli' => 'Pembeli Orphan',
        'toko_id' => $this->toko->id,
        'subtotal' => 1000000,
        'status' => 1,
        'ppn' => 0,
        'show_infopembayaran' => 0,
    ]);

    // Imported sales detached the unit by storing 0 in gudang_barang_id, and the
    // unit was soft deleted at the exact moment the sale was created.
    $unit->forceFill(['deleted_at' => $penjualan->created_at])->save();

    DetailPenjualan::create([
        'penjualan_id' => $penjualan->id,
        'barang_id' => $this->barang->id,
        'gudang_barang_id' => 0,
        'serial_number_id' => null,
        'price' => 1000000,
        'discount' => 0,
    ]);

    expect(LineItems::unitOptions($this->barang->id, $this->toko->id))
        ->not->toHaveKey($unit->id);

    Livewire::test(EditPenjualan::class, ['record' => $penjualan->getRouteKey()])
        ->fillForm(['items' => []])
        ->call('save')
        ->assertHasNoFormErrors();

    $restored = GudangBarang::withTrashed()->find($unit->id);

    expect($restored->deleted_at)->toBeNull()
        ->and((int) $restored->status)->toBe(1)
        ->and($restored->resolvePenjualan())->toBeNull();

    expect(LineItems::unitOptions($this->barang->id, $this->toko->id))
        ->toHaveKey($unit->id);
});

test('editing an imported sale recovers a detached unit removed earlier', function () {
    $unit = GudangBarang::create([
        'barang_id' => $this->barang->id,
        'serial_number_id' => 'SN-STUCK',
        'toko_id' => $this->toko->id,
        'status' => 1,
    ]);

    $penjualan = Penjualan::create([
        'date' => now()->toDateString(),
        'kode_penjualan' => 'PJ-STUCK',
        'nama_pembeli' => 'Pembeli Stuck',
        'toko_id' => $this->toko->id,
        'subtotal' => 1000000,
        'status' => 1,
        'ppn' => 0,
        'show_infopembayaran' => 0,
    ]);

    $unit->forceFill(['deleted_at' => $penjualan->created_at])->save();

    // The line was removed earlier but the detached unit was never returned.
    $detail = DetailPenjualan::create([
        'penjualan_id' => $penjualan->id,
        'barang_id' => $this->barang->id,
        'gudang_barang_id' => 0,
        'serial_number_id' => null,
        'price' => 1000000,
        'discount' => 0,
    ]);
    $detail->delete();

    $penjualan->restoreSoldUnits();

    $restored = GudangBarang::withTrashed()->find($unit->id);

    expect($restored->deleted_at)->toBeNull()
        ->and((int) $restored->status)->toBe(1);
});

test('serial number options include older stock units beyond fifty rows', function () {
    $oldest = null;

    for ($i = 0; $i < 60; $i++) {
        $unit = GudangBarang::create([
            'barang_id' => $this->barang->id,
            'serial_number_id' => 'SN-'.$i,
            'toko_id' => $this->toko->id,
            'status' => 1,
        ]);

        $oldest ??= $unit;
    }

    $options = LineItems::unitOptions($this->barang->id, $this->toko->id);

    expect($options)->toHaveCount(60)
        ->and($options)->toHaveKey($oldest->id);
});

test('deleting a penjualan returns its stock units to available stock', function () {
    $unit = GudangBarang::create([
        'barang_id' => $this->barang->id,
        'serial_number_id' => 'SN-DELETE',
        'toko_id' => $this->toko->id,
        'status' => 1,
    ]);

    Livewire::test(CreatePenjualan::class)
        ->fillForm([
            'date' => now()->toDateString(),
            'kode_penjualan' => 'PJ-DELETE',
            'nama_pembeli' => 'Pembeli Delete',
            'toko_id' => $this->toko->id,
            'items' => [
                ['barang_id' => $this->barang->id, 'jumlah' => 1, 'serial_numbers' => [$unit->id], 'price' => 1000000, 'discount' => 0],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    Penjualan::first()->delete();

    $restored = GudangBarang::withTrashed()->find($unit->id);

    expect($restored->deleted_at)->toBeNull()
        ->and((int) $restored->status)->toBe(1)
        ->and(DetailPenjualan::withTrashed()->where('gudang_barang_id', $unit->id)->whereNull('deleted_at')->count())->toBe(0);
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

test('create purchase order can be saved as draft', function () {
    Livewire::test(CreatePo::class)
        ->fillForm([
            'date' => now()->toDateString(),
            'kode_po' => 'PO-DRAFT-NEW',
            'toko_id' => $this->toko->id,
            'items' => [
                ['barang_id' => $this->barang->id, 'jumlah' => 1, 'price' => 1000000, 'discount' => 0],
            ],
        ])
        ->call('saveDraft')
        ->assertHasNoFormErrors();

    expect((int) Po::where('kode_po', 'PO-DRAFT-NEW')->first()->status)->toBe(2);
});

test('edit purchase order can be saved as draft and back to dikirim', function () {
    $po = Po::create([
        'date' => now()->toDateString(),
        'kode_po' => 'PO-DRAFT-EDIT',
        'toko_id' => $this->toko->id,
        'status' => 1,
    ]);

    PoDetail::create([
        'po_id' => $po->id,
        'barang_id' => $this->barang->id,
        'jumlah' => 1,
        'price' => 1000000,
        'discount' => 0,
        'subtotal' => 1000000,
    ]);

    Livewire::test(EditPo::class, ['record' => $po->getRouteKey()])
        ->call('saveDraft')
        ->assertHasNoFormErrors();

    expect((int) $po->refresh()->status)->toBe(2);

    Livewire::test(EditPo::class, ['record' => $po->getRouteKey()])
        ->call('save')
        ->assertHasNoFormErrors();

    expect((int) $po->refresh()->status)->toBe(1);
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
