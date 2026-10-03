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
