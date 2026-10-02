<?php

use App\Models\Barang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are redirected from the barang pdf export', function () {
    $this->get(route('barang.export.pdf'))
        ->assertRedirect(route('filament.admin.auth.login'));
});

test('authenticated users can export barang to pdf', function () {
    Barang::create([
        'nama_product' => 'Amplifier Test',
        'merk' => 'Denon',
        'satuan' => 'bh',
        'harga' => 10000000,
        'wajib_serial_number' => true,
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('barang.export.pdf'));

    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');
    expect($response->streamedContent())->toStartWith('%PDF');
});
