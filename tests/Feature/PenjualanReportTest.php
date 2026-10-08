<?php

use App\Filament\Pages\Reports\PenjualanReport;
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

/**
 * @return array{0: Penjualan, 1: Barang, 2: Toko}
 */
function createPenjualanReport(): array
{
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

    return [$penjualan, $barang, $toko];
}

test('laporan penjualan berdasarkan barang memakai kolom per barang', function () {
    createPenjualanReport();

    $report = app(ReportController::class)->penjualan(['jenis_report' => '1']);

    expect($report['headings'])->toBe([
        'No',
        'No Ref',
        'Nama Barang',
        'Serial Number',
        'Nama Toko',
        'Tanggal Keluar',
        'Nama Pembeli',
        'Alamat Pembeli',
        'No Telepon',
        'Harga Terjual',
        'Sales',
    ]);
});

test('laporan penjualan berdasarkan penjualan memakai kolom per transaksi', function () {
    createPenjualanReport();

    $report = app(ReportController::class)->penjualan(['jenis_report' => '2']);

    expect($report['headings'])->toBe([
        'No',
        'Tanggal',
        'Kode Penjualan',
        'Nama Pembeli',
        'Cara Bayar',
        'Nama Toko',
        'Status Bayar',
        'Total Pembayaran',
        'DP',
        'Sisa',
        'Nama Project',
    ]);

    expect($report['rows'][0][2])->toBe('PJ-1')
        ->and($report['rows'][0][7])->toBe('1.000.000');
});

test('laporan penjualan berdasarkan barang menampilkan serial number dari unit stok', function () {
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

    $report = app(ReportController::class)->penjualan(['jenis_report' => '1']);

    expect($report['rows'][0][3])->toBe('23120626');
});

test('laporan penjualan memperbarui kolom saat jenis laporan diganti', function () {
    $this->actingAs(User::factory()->create(['status' => 1]));
    createPenjualanReport();

    $component = Livewire::test(PenjualanReport::class);

    $labels = fn (): array => array_map(
        fn ($column): string => $column->getLabel(),
        $component->instance()->getTable()->getColumns(),
    );

    $component->set('data.jenis_report', '2');
    expect($labels())
        ->toContain('Kode Penjualan')
        ->toContain('Total Pembayaran')
        ->not->toContain('No Ref');

    $component->set('data.jenis_report', '1');
    expect($labels())
        ->toContain('No Ref')
        ->toContain('Nama Barang')
        ->not->toContain('Kode Penjualan');
});

test('laporan penjualan dapat difilter berdasarkan status bayar', function () {
    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker',
    ]);

    $makePenjualan = function (string $kode, string $status) use ($toko, $barang): void {
        $penjualan = Penjualan::create([
            'date' => now()->toDateString(),
            'kode_penjualan' => $kode,
            'nama_pembeli' => 'Pembeli',
            'toko_id' => $toko->id,
            'subtotal' => 1000000,
            'status' => 1,
            'ppn' => 0,
            'show_infopembayaran' => 0,
            'payment_status' => $status,
        ]);

        DetailPenjualan::create([
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang->id,
            'price' => 1000000,
            'discount' => 0,
        ]);
    };

    $makePenjualan('PJ-LUNAS', 'Lunas');
    $makePenjualan('PJ-DP', 'DP');

    $controller = app(ReportController::class);

    $kodeUntuk = fn (string $status): array => array_column(
        array_slice($controller->penjualan(['jenis_report' => '2', 'status_bayar' => $status])['rows'], 0, -1),
        2,
    );

    expect($kodeUntuk('Lunas'))->toBe(['PJ-LUNAS'])
        ->and($kodeUntuk('DP'))->toBe(['PJ-DP']);
});

test('laporan penjualan berdasarkan barang hanya menampilkan barang yang dipilih', function () {
    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);

    $speaker = Barang::create(['kategori_id' => $kategori->id, 'nama_product' => 'Speaker']);
    $mic = Barang::create(['kategori_id' => $kategori->id, 'nama_product' => 'Microphone']);

    $penjualan = Penjualan::create([
        'date' => now()->toDateString(),
        'kode_penjualan' => 'PJ-MIX',
        'nama_pembeli' => 'Pembeli A',
        'toko_id' => $toko->id,
        'subtotal' => 2000000,
        'status' => 1,
        'ppn' => 0,
        'show_infopembayaran' => 0,
    ]);

    $speakerUnit = GudangBarang::create([
        'barang_id' => $speaker->id,
        'serial_number_id' => 'SPK-1',
        'toko_id' => $toko->id,
        'status' => 1,
    ]);

    $micUnit = GudangBarang::create([
        'barang_id' => $mic->id,
        'serial_number_id' => 'MIC-1',
        'toko_id' => $toko->id,
        'status' => 1,
    ]);

    DetailPenjualan::create([
        'penjualan_id' => $penjualan->id,
        'barang_id' => $speaker->id,
        'gudang_barang_id' => $speakerUnit->id,
        'price' => 1000000,
        'discount' => 0,
    ]);

    DetailPenjualan::create([
        'penjualan_id' => $penjualan->id,
        'barang_id' => $mic->id,
        'gudang_barang_id' => $micUnit->id,
        'price' => 1000000,
        'discount' => 0,
    ]);

    $report = app(ReportController::class)->penjualan([
        'jenis_report' => '1',
        'nama_barang' => [$speaker->id],
    ]);

    expect($report['rows'])->toHaveCount(1)
        ->and($report['rows'][0][2])->toBe('Speaker')
        ->and($report['rows'][0][3])->toBe('SPK-1');
});
