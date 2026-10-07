<?php

use App\Models\IpWhitelistSetting;
use App\Models\User;
use App\Models\WhitelistedIp;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('all requests are allowed while the whitelist is disabled', function () {
    $user = User::factory()->create(['status' => 2, 'user_menu' => 'Barang']);

    $this->actingAs($user)
        ->withServerVariables(['REMOTE_ADDR' => '10.0.0.9'])
        ->get('/admin/barangs')
        ->assertSuccessful();
});

test('a non whitelisted ip is blocked while the whitelist is enabled', function () {
    IpWhitelistSetting::setEnabled(true);

    $user = User::factory()->create(['status' => 2, 'user_menu' => 'Barang']);

    $this->actingAs($user)
        ->withServerVariables(['REMOTE_ADDR' => '10.0.0.9'])
        ->get('/admin/barangs')
        ->assertForbidden();
});

test('a whitelisted ip is allowed while the whitelist is enabled', function () {
    IpWhitelistSetting::setEnabled(true);
    WhitelistedIp::factory()->create(['ip_address' => '10.0.0.5']);

    $user = User::factory()->create(['status' => 2, 'user_menu' => 'Barang']);

    $this->actingAs($user)
        ->withServerVariables(['REMOTE_ADDR' => '10.0.0.5'])
        ->get('/admin/barangs')
        ->assertSuccessful();
});

test('superadmin bypasses the whitelist', function () {
    IpWhitelistSetting::setEnabled(true);

    $superadmin = User::factory()->create();

    $this->actingAs($superadmin)
        ->withServerVariables(['REMOTE_ADDR' => '10.0.0.9'])
        ->get('/admin/whitelisted-ips')
        ->assertSuccessful();
});

test('guests can still reach the login page while the whitelist is enabled', function () {
    IpWhitelistSetting::setEnabled(true);

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.9'])
        ->get('/admin/login')
        ->assertSuccessful();
});

test('only superadmin can access the whitelist resource', function () {
    $superadmin = User::factory()->create();
    $this->actingAs($superadmin)->get('/admin/whitelisted-ips')->assertSuccessful();
    $this->actingAs($superadmin)->get('/admin/whitelisted-ips/create')->assertSuccessful();

    $admin = User::factory()->create(['status' => 2, 'user_menu' => 'User']);
    $this->actingAs($admin)->get('/admin/whitelisted-ips')->assertForbidden();

    $adminPusat = User::factory()->create(['status' => 1, 'status_admin' => 1]);
    $this->actingAs($adminPusat)->get('/admin/whitelisted-ips')->assertForbidden();
});

test('the whitelist toggle persists its state', function () {
    expect(IpWhitelistSetting::isEnabled())->toBeFalse();

    IpWhitelistSetting::setEnabled(true);
    expect(IpWhitelistSetting::isEnabled())->toBeTrue();

    IpWhitelistSetting::setEnabled(false);
    expect(IpWhitelistSetting::isEnabled())->toBeFalse();
});
