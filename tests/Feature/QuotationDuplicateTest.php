<?php

use App\Filament\Resources\Quotations\Pages\EditQuotation;
use App\Filament\Resources\Quotations\Pages\ListQuotations;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('duplicates a quotation with its line items', function () {
    $this->actingAs(User::factory()->create(['status' => 1]));
    $this->travelTo('2026-06-15 10:00:00');

    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker',
        'harga' => 1000000,
    ]);

    $quotation = Quotation::create([
        'date' => '2026-01-05',
        'kode_quotation' => 'QT - 0000001',
        'nama_pembeli' => 'Pembeli Lama',
        'alamat_pembeli' => 'Jl. Lama',
        'telepon' => '08123456789',
        'toko_id' => $toko->id,
        'nama_sales' => 'Sales A',
        'subtotal' => 1800000,
        'ppn' => 198000,
        'status' => 1,
        'keterangan' => 'Catatan pembayaran',
        'show_infopembayaran' => 1,
        'show_option' => 1,
        'option_text' => 'Opsi tambahan',
        'show_project' => 1,
        'nama_project' => 'Project A',
    ]);

    QuotationDetail::create([
        'quotation_id' => $quotation->id,
        'barang_id' => $barang->id,
        'jumlah' => 2,
        'discount' => 10,
        'price' => 1000000,
        'subtotal' => 1800000,
    ]);

    $component = Livewire::test(ListQuotations::class)
        ->callTableAction('duplicate', $quotation);

    $duplicate = Quotation::query()->whereKeyNot($quotation->getKey())->sole();

    $component->assertRedirect(EditQuotation::getUrl(['record' => $duplicate]));

    expect($duplicate->kode_quotation)->toBe('QT - 0000002')
        ->and($duplicate->date)->toBe('2026-06-15')
        ->and($duplicate->nama_pembeli)->toBe('Pembeli Lama')
        ->and($duplicate->alamat_pembeli)->toBe('Jl. Lama')
        ->and($duplicate->telepon)->toBe('08123456789')
        ->and($duplicate->toko_id)->toBe($toko->id)
        ->and($duplicate->nama_sales)->toBe('Sales A')
        ->and((float) $duplicate->subtotal)->toBe(1800000.0)
        ->and((float) $duplicate->ppn)->toBe(198000.0)
        ->and((int) $duplicate->status)->toBe(1)
        ->and($duplicate->keterangan)->toBe('Catatan pembayaran')
        ->and((int) $duplicate->show_infopembayaran)->toBe(1)
        ->and((int) $duplicate->show_option)->toBe(1)
        ->and($duplicate->option_text)->toBe('Opsi tambahan')
        ->and((int) $duplicate->show_project)->toBe(1)
        ->and($duplicate->nama_project)->toBe('Project A');

    $copiedDetail = $duplicate->detail_quotation()->sole();

    expect($copiedDetail->barang_id)->toBe($barang->id)
        ->and((int) $copiedDetail->jumlah)->toBe(2)
        ->and((int) $copiedDetail->discount)->toBe(10)
        ->and((float) $copiedDetail->price)->toBe(1000000.0)
        ->and((float) $copiedDetail->subtotal)->toBe(1800000.0);

    expect($quotation->fresh()->kode_quotation)->toBe('QT - 0000001');
});
