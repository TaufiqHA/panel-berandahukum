<?php

use App\Filament\Resources\Quotations\Pages\CreateQuotation;
use App\Filament\Resources\Quotations\Pages\EditQuotation;
use App\Filament\Support\LineItems;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function makeQuotationBarang(int $harga): Barang
{
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);

    return Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker',
        'harga' => $harga,
    ]);
}

test('mengisi harga quotation dari price list barang saat barang dipilih', function () {
    $this->actingAs(User::factory()->create(['status' => 1]));

    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $barang = makeQuotationBarang(1385000);

    $component = Livewire::test(CreateQuotation::class)
        ->fillForm([
            'date' => now()->toDateString(),
            'kode_quotation' => 'QT - 0000001',
            'toko_id' => $toko->id,
            'nama_pembeli' => 'Pembeli A',
        ]);

    $key = array_key_first($component->get('data.items'));

    $component->set("data.items.{$key}.barang_id", $barang->id);

    expect((float) $component->get("data.items.{$key}.price"))->toBe(1385000.0);

    $component->call('create')->assertHasNoFormErrors();

    expect((float) QuotationDetail::sole()->price)->toBe(1385000.0);
});

test('mengisi harga kosong dari price list barang saat edit quotation dibuka', function () {
    $this->actingAs(User::factory()->create(['status' => 1]));

    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $barang = makeQuotationBarang(1385000);

    $quotation = Quotation::create([
        'date' => now()->toDateString(),
        'kode_quotation' => 'QT - 0000001',
        'nama_pembeli' => 'Pembeli A',
        'toko_id' => $toko->id,
        'subtotal' => 0,
        'ppn' => 0,
        'status' => 1,
        'show_infopembayaran' => 0,
    ]);

    QuotationDetail::create([
        'quotation_id' => $quotation->id,
        'barang_id' => $barang->id,
        'jumlah' => 1,
        'discount' => 0,
        'price' => 0,
        'subtotal' => 0,
    ]);

    $component = Livewire::test(EditQuotation::class, ['record' => $quotation->getRouteKey()]);

    $key = array_key_first($component->get('data.items'));

    expect((float) $component->get("data.items.{$key}.price"))->toBe(1385000.0);
});

test('harga negatif tidak ditimpa price list saat edit quotation', function () {
    $this->actingAs(User::factory()->create(['status' => 1]));

    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $barang = makeQuotationBarang(1000000);

    $quotation = Quotation::create([
        'date' => now()->toDateString(),
        'kode_quotation' => 'QT - 0000002',
        'nama_pembeli' => 'Pembeli A',
        'toko_id' => $toko->id,
        'subtotal' => -500000,
        'ppn' => 0,
        'status' => 1,
        'show_infopembayaran' => 0,
    ]);

    QuotationDetail::create([
        'quotation_id' => $quotation->id,
        'barang_id' => $barang->id,
        'jumlah' => 1,
        'discount' => 0,
        'price' => -500000,
        'subtotal' => -500000,
    ]);

    $component = Livewire::test(EditQuotation::class, ['record' => $quotation->getRouteKey()]);

    $key = array_key_first($component->get('data.items'));

    expect(LineItems::toNumber($component->get("data.items.{$key}.price")))->toBe(-500000.0);

    $component->call('save')->assertHasNoFormErrors();

    expect((float) QuotationDetail::where('quotation_id', $quotation->id)->sole()->price)->toBe(-500000.0)
        ->and((float) $quotation->refresh()->subtotal)->toBe(-500000.0);
});
