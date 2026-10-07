<?php

use App\Filament\Pages\Reports\LabaRugiReport;
use App\Http\Controllers\ReportController;
use App\Models\Barang;
use App\Models\DetailPenjualan;
use App\Models\GudangBarang;
use App\Models\Kategori;
use App\Models\Penjualan;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('laporan laba rugi berdasarkan penjualan memakai kolom yang diinginkan', function () {
    $report = app(ReportController::class)->labaRugi(['jenis_report' => '2']);

    expect($report['headings'])->toBe([
        'No',
        'Tanggal',
        'Kode Penjualan',
        'Nama Pembeli',
        'Nama Toko',
        'Status Bayar',
        'Total Pembayaran',
        'Total Hrg.Beli',
        'Keuntungan',
        'Nama Project',
    ]);
});

test('laporan laba rugi memperbarui kolom saat jenis laporan diganti', function () {
    $this->actingAs(User::factory()->create(['status' => 1]));

    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker',
    ]);

    $penjualan = Penjualan::create([
        'date' => now()->toDateString(),
        'kode_penjualan' => 'PJ-1',
        'nama_pembeli' => 'Pembeli A',
        'toko_id' => $toko->id,
        'subtotal' => 1000000,
        'status' => 1,
        'ppn' => 0,
        'show_infopembayaran' => 0,
    ]);

    DetailPenjualan::create([
        'penjualan_id' => $penjualan->id,
        'barang_id' => $barang->id,
        'price' => 1000000,
        'discount' => 0,
    ]);

    $component = Livewire::test(LabaRugiReport::class);

    $labels = fn (): array => array_map(
        fn ($column): string => $column->getLabel(),
        $component->instance()->getTable()->getColumns(),
    );

    // Berdasarkan Penjualan
    $component->set('data.jenis_report', '2');
    expect($labels())
        ->toContain('Kode Penjualan')
        ->toContain('Total Hrg.Beli')
        ->not->toContain('No Ref');

    // Berdasarkan Barang
    $component->set('data.jenis_report', '1');
    expect($labels())
        ->toContain('No Ref')
        ->toContain('Harga Beli')
        ->toContain('Harga Jual')
        ->toContain('Keuntungan')
        ->not->toContain('Total Hrg.Beli');
});

test('laporan laba rugi berdasarkan barang menampilkan serial number dari unit stok', function () {
    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker Bose',
    ]);

    $penjualan = Penjualan::create([
        'date' => now()->toDateString(),
        'kode_penjualan' => 'PJ-SERIAL',
        'nama_pembeli' => 'Pembeli A',
        'toko_id' => $toko->id,
        'subtotal' => 1000000,
        'status' => 1,
        'ppn' => 0,
        'show_infopembayaran' => 0,
        'payment_status' => 'Lunas',
        'sisa' => 0,
    ]);

    $unit = GudangBarang::create([
        'barang_id' => $barang->id,
        'serial_number_id' => '23120626',
        'toko_id' => $toko->id,
        'status' => 1,
    ]);

    DetailPenjualan::create([
        'penjualan_id' => $penjualan->id,
        'barang_id' => $barang->id,
        'gudang_barang_id' => $unit->id,
        // Legacy bug: this column wrongly holds the stock unit id.
        'serial_number_id' => $unit->id,
        'price' => 1000000,
        'discount' => 0,
    ]);

    $report = app(ReportController::class)->labaRugi(['jenis_report' => '1']);

    expect($report['rows'][0][4])->toBe('23120626');
});

test('laporan laba rugi hanya memasukkan penjualan yang sudah lunas', function () {
    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker',
    ]);

    $makePenjualan = function (string $kode, ?string $paymentStatus, float $sisa) use ($toko, $barang): void {
        $penjualan = Penjualan::create([
            'date' => now()->toDateString(),
            'kode_penjualan' => $kode,
            'nama_pembeli' => 'Pembeli',
            'toko_id' => $toko->id,
            'subtotal' => 1000000,
            'status' => 1,
            'ppn' => 0,
            'show_infopembayaran' => 0,
            'payment_status' => $paymentStatus,
            'sisa' => $sisa,
        ]);

        DetailPenjualan::create([
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang->id,
            'price' => 1000000,
            'discount' => 0,
        ]);
    };

    $makePenjualan('PJ-LUNAS', 'Lunas', 0);
    $makePenjualan('PJ-CBD', 'Cash Before Delivery (CBD)', 0);
    $makePenjualan('PJ-DP', 'DP', 500000);
    $makePenjualan('PJ-TEMPO', 'Tempo', 1000000);

    // Berdasarkan Penjualan: satu baris per transaksi (tanpa baris TOTAL).
    $perPenjualan = app(ReportController::class)->labaRugi(['jenis_report' => '2']);
    $kodePenjualan = array_column(array_slice($perPenjualan['rows'], 0, -1), 2);
    expect($kodePenjualan)->toEqualCanonicalizing(['PJ-LUNAS', 'PJ-CBD']);

    // Berdasarkan Barang: satu baris per detail barang.
    $perBarang = app(ReportController::class)->labaRugi(['jenis_report' => '1']);
    $kodeBarang = array_column(array_slice($perBarang['rows'], 0, -1), 2);
    expect($kodeBarang)->toEqualCanonicalizing(['PJ-LUNAS', 'PJ-CBD']);
});
