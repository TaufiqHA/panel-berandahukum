<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin with a limited menu can only access menu items in user_menu', function () {
    $user = User::factory()->create([
        'status' => 2,
        'user_menu' => 'Barang,Kategori Barang',
    ]);

    $this->actingAs($user);

    $this->get('/admin/barangs')->assertSuccessful();
    $this->get('/admin/kategoris')->assertSuccessful();

    $this->get('/admin/pos')->assertForbidden();
    $this->get('/admin/penjualans')->assertForbidden();
    $this->get('/admin/tokos')->assertForbidden();
});

test('superadmin can access everything', function () {
    $user = User::factory()->create(['status' => 1]);

    $this->actingAs($user);

    $this->get('/admin/barangs')->assertSuccessful();
    $this->get('/admin/users')->assertSuccessful();
    $this->get('/admin/stock-report')->assertSuccessful();
});

test('admin pusat is blocked from user management and report', function () {
    $user = User::factory()->create([
        'status' => 1,
        'status_admin' => 1,
    ]);

    $this->actingAs($user);

    $this->get('/admin/barangs')->assertSuccessful();
    $this->get('/admin/users')->assertForbidden();
    $this->get('/admin/stock-report')->assertForbidden();
});
