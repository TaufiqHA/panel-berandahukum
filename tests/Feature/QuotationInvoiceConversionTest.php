<?php

use App\Filament\Resources\Invoices\Pages\EditInvoice;
use App\Filament\Resources\Quotations\Pages\ListQuotations;
use App\Models\Barang;
use App\Models\Invoice;
use App\Models\Kategori;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('creates an invoice from a quotation with its line items', function () {
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
        'subtotal' => 2300000,
        'ppn' => 253000,
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

    QuotationDetail::create([
        'quotation_id' => $quotation->id,
        'barang_id' => $barang->id,
        'jumlah' => 1,
        'discount' => 0,
        'price' => 500000,
        'subtotal' => 500000,
    ]);

    $component = Livewire::test(ListQuotations::class)
        ->callTableAction('invoice', $quotation);

    $invoice = Invoice::query()->sole();

    $component->assertRedirect(EditInvoice::getUrl(['record' => $invoice]));

    expect($invoice->quotation_id)->toBe($quotation->id)
        ->and($invoice->kode_invoice)->toBe('INV - 0000001')
        ->and($invoice->date)->toBe('2026-06-15')
        ->and($invoice->nama_pembeli)->toBe('Pembeli Lama')
        ->and($invoice->alamat_pembeli)->toBe('Jl. Lama')
        ->and($invoice->tlp_pembeli)->toBe('08123456789')
        ->and($invoice->toko_id)->toBe($toko->id)
        ->and($invoice->nama_sales)->toBe('Sales A')
        ->and($invoice->metode_pembayaran)->toBe('Tunai')
        ->and($invoice->payment_status)->toBeNull()
        ->and((float) $invoice->subtotal)->toBe(2300000.0)
        ->and((float) $invoice->ppn)->toBe(253000.0)
        ->and((float) $invoice->total_pembayaran)->toBe(2553000.0)
        ->and((float) $invoice->dp_payment)->toBe(0.0)
        ->and((float) $invoice->sisa)->toBe(0.0)
        ->and((int) $invoice->status)->toBe(1)
        ->and($invoice->keterangan)->toBe('Catatan pembayaran')
        ->and((int) $invoice->show_infopembayaran)->toBe(1)
        ->and((int) $invoice->show_option)->toBe(1)
        ->and($invoice->option_text)->toBe('Opsi tambahan')
        ->and((int) $invoice->show_project)->toBe(1)
        ->and($invoice->nama_project)->toBe('Project A');

    $copiedDetails = $invoice->detail_invoice()->orderBy('id')->get();

    expect($copiedDetails)->toHaveCount(2)
        ->and($copiedDetails[0]->barang_id)->toBe($barang->id)
        ->and((int) $copiedDetails[0]->jumlah)->toBe(2)
        ->and((int) $copiedDetails[0]->discount)->toBe(10)
        ->and((float) $copiedDetails[0]->price)->toBe(1000000.0)
        ->and((float) $copiedDetails[0]->subtotal)->toBe(1800000.0)
        ->and((int) $copiedDetails[1]->jumlah)->toBe(1)
        ->and((int) $copiedDetails[1]->discount)->toBe(0)
        ->and((float) $copiedDetails[1]->price)->toBe(500000.0)
        ->and((float) $copiedDetails[1]->subtotal)->toBe(500000.0);
});

test('does not create a second invoice for the same quotation', function () {
    $this->actingAs(User::factory()->create(['status' => 1]));

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
        'toko_id' => $toko->id,
        'nama_sales' => 'Sales A',
        'subtotal' => 1000000,
        'ppn' => 0,
        'status' => 1,
        'show_infopembayaran' => 0,
    ]);

    QuotationDetail::create([
        'quotation_id' => $quotation->id,
        'barang_id' => $barang->id,
        'jumlah' => 1,
        'discount' => 0,
        'price' => 1000000,
        'subtotal' => 1000000,
    ]);

    Livewire::test(ListQuotations::class)
        ->callTableAction('invoice', $quotation);

    $invoice = Invoice::query()->sole();

    session()->forget(['filament.notifications', 'filament.claimed_notifications']);

    $component = Livewire::test(ListQuotations::class)
        ->callTableAction('invoice', $quotation);

    expect(Invoice::query()->count())->toBe(1);

    $component->assertRedirect(EditInvoice::getUrl(['record' => $invoice]))
        ->assertNotified('Quotation sudah dibuatkan invoice');
});

test('hides the invoice action when the quotation has no toko', function () {
    $this->actingAs(User::factory()->create(['status' => 1]));

    $quotation = Quotation::create([
        'date' => '2026-01-05',
        'kode_quotation' => 'QT - 0000001',
        'nama_pembeli' => 'Pembeli Lama',
        'nama_sales' => 'Sales A',
        'subtotal' => 1000000,
        'ppn' => 0,
        'status' => 1,
        'show_infopembayaran' => 0,
    ]);

    Livewire::test(ListQuotations::class)
        ->assertTableActionHidden('invoice', $quotation);
});
