<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated users are redirected from admin panel to login', function () {
    $response = $this->get('/admin');

    $response->assertRedirect('/admin/login');
});

test('admin login page is accessible', function () {
    $response = $this->get('/admin/login');

    $response->assertSuccessful();
});

test('authenticated users can access the admin dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/admin');

    $response->assertSuccessful();
});
