<?php

use App\Http\Controllers\ReportController;
use App\Models\Po;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('laporan purchase order hanya menampilkan status dikirim', function () {
    $this->actingAs(User::factory()->create(['status' => 1]));

    $date = now()->toDateString();

    Po::create(['kode_po' => 'PO-DIKIRIM', 'date' => $date, 'status' => 1]);
    Po::create(['kode_po' => 'PO-DRAFT', 'date' => $date, 'status' => 2]);
    Po::create(['kode_po' => 'PO-CANCELED', 'date' => $date, 'status' => 3]);

    $report = app(ReportController::class)->po([]);

    $kodePo = array_map(fn (array $row): string => (string) ($row[2] ?? ''), $report['rows']);

    expect($kodePo)->toContain('PO-DIKIRIM')
        ->not->toContain('PO-DRAFT')
        ->not->toContain('PO-CANCELED');
});

test('laporan purchase order memfilter jatuh tempo tanpa membatasi tanggal po', function () {
    $this->actingAs(User::factory()->create(['status' => 1]));

    $jatuhTempoBulanIni = now()->startOfMonth()->addDays(9)->toDateString();
    $tanggalLuarBulanIni = now()->subMonth()->toDateString();

    Po::create([
        'kode_po' => 'PO-DALAM-BULAN',
        'date' => now()->toDateString(),
        'jatuh_tempo' => $jatuhTempoBulanIni,
        'status' => 1,
    ]);

    Po::create([
        'kode_po' => 'PO-LUAR-BULAN',
        'date' => $tanggalLuarBulanIni,
        'jatuh_tempo' => $jatuhTempoBulanIni,
        'status' => 1,
    ]);

    $report = app(ReportController::class)->po([
        'jatuh_tempo_awal' => now()->startOfMonth()->toDateString(),
        'jatuh_tempo_akhir' => now()->endOfMonth()->toDateString(),
    ]);

    $kodePo = array_map(fn (array $row): string => (string) ($row[2] ?? ''), $report['rows']);

    expect($kodePo)->toContain('PO-DALAM-BULAN')
        ->toContain('PO-LUAR-BULAN');
});

test('laporan purchase order memakai kolom yang diinginkan', function () {
    $this->actingAs(User::factory()->create(['status' => 1]));

    $report = app(ReportController::class)->po([]);

    expect($report['headings'])->toBe([
        'Id',
        'Tanggal',
        'Kode PO',
        'Nama Supplier',
        'Total Pembayaran',
        'Status PO',
        'Status Barang',
        'Status Bayar',
        'Jatuh Tempo',
    ]);
});
