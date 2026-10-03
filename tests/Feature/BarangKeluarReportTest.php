<?php

use App\Http\Controllers\ReportController;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\DataBarangKeluar;
use App\Models\Kategori;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('laporan barang keluar memakai kolom yang diinginkan dan memuat keterangan', function () {
    $this->actingAs(User::factory()->create(['status' => 1]));

    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);
    $kategori = Kategori::create(['nama_kategori' => 'Audio']);
    $barang = Barang::create([
        'kategori_id' => $kategori->id,
        'nama_product' => 'Speaker',
        'harga' => 1000000,
    ]);

    $barangKeluar = BarangKeluar::create([
        'date' => now()->toDateString(),
        'kode_barang_keluar' => 'BK - 0000001',
        'nama_penerima' => 'Penerima Test',
        'toko_id' => $toko->id,
        'keterangan' => 'Barang rusak',
        'status' => 1,
    ]);

    DataBarangKeluar::create([
        'barang_keluar_id' => $barangKeluar->id,
        'barang_id' => $barang->id,
    ]);

    $report = app(ReportController::class)->barangKeluar([]);

    expect($report['headings'])->toBe([
        'No',
        'No Ref',
        'Nama Barang',
        'Serial Number',
        'Nama Toko',
        'Tanggal Keluar',
        'Nama Penerima',
        'Harga',
        'Keterangan',
    ]);

    expect($report['rows'])->toHaveCount(1)
        ->and($report['rows'][0][8])->toBe('Barang rusak');
});
