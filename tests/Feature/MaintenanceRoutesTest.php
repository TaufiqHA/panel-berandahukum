<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

test('guests are redirected from the maintenance routes', function () {
    $this->get(route('maintenance.index'))
        ->assertRedirect(route('filament.admin.auth.login'));

    $this->get(route('maintenance.migrate'))
        ->assertRedirect(route('filament.admin.auth.login'));
});

test('non admin users cannot run maintenance commands', function () {
    $user = User::factory()->create(['status' => 2]);

    $this->actingAs($user)
        ->get(route('maintenance.migrate'))
        ->assertForbidden();
});

test('admins can open the maintenance index', function () {
    $user = User::factory()->create(['status' => 1]);

    $this->actingAs($user)
        ->get(route('maintenance.index'))
        ->assertSuccessful()
        ->assertSee('Migrate', false);
});

test('admins can run migrate', function () {
    $user = User::factory()->create(['status' => 1]);

    $this->actingAs($user)
        ->get(route('maintenance.migrate'))
        ->assertSuccessful()
        ->assertSee('$ migrate', false);
});

test('maintenance commands require the configured token', function () {
    config(['app.maintenance_token' => 'super-secret']);

    $user = User::factory()->create(['status' => 1]);

    $this->actingAs($user)
        ->get(route('maintenance.migrate'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('maintenance.migrate', ['token' => 'super-secret']))
        ->assertSuccessful();
});

test('admins can run optimize and it can be cleared afterwards', function () {
    $user = User::factory()->create(['status' => 1]);

    try {
        $this->actingAs($user)
            ->get(route('maintenance.optimize'))
            ->assertSuccessful()
            ->assertSee('$ optimize', false);
    } finally {
        Artisan::call('optimize:clear');
    }
});
