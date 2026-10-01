<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('master resource pages are accessible', function (string $path) {
    $user = User::factory()->create();

    $this->actingAs($user)->get($path)->assertSuccessful();
})->with([
    '/admin/barangs',
    '/admin/barangs/create',
    '/admin/kategoris',
    '/admin/kategoris/create',
    '/admin/tokos',
    '/admin/tokos/create',
    '/admin/suppliers',
    '/admin/suppliers/create',
    '/admin/settings',
    '/admin/settings/create',
    '/admin/signatures',
    '/admin/signatures/create',
    '/admin/users',
    '/admin/users/create',
    '/admin/stock-ins',
    '/admin/stock-ins/create',
    '/admin/pindah-gudangs',
    '/admin/pindah-gudangs/create',
    '/admin/pindah-toko-ins',
    '/admin/gudang-barangs',
    '/admin/stock',
    '/admin/search-barang',
    '/admin/barang-masuk-report',
    '/admin/penjualan-report',
    '/admin/pindah-barang-report',
    '/admin/stock-report',
    '/admin/laba-rugi-report',
    '/admin/po-report',
    '/admin/pos',
    '/admin/pos/create',
    '/admin/quotations',
    '/admin/quotations/create',
    '/admin/invoices',
    '/admin/invoices/create',
    '/admin/penjualans',
    '/admin/penjualans/create',
    '/admin/barang-keluars',
    '/admin/barang-keluars/create',
]);
