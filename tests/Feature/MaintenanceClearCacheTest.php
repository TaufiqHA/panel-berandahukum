<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are redirected from the clear cache route', function () {
    $this->get(route('maintenance.clear-cache'))
        ->assertRedirect(route('filament.admin.auth.login'));
});

test('authenticated users can clear the application cache', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('maintenance.clear-cache'))
        ->assertSuccessful();
});
