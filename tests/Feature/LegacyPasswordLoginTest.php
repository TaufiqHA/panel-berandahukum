<?php

use App\Filament\Pages\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('a user with a legacy $2a$ bcrypt hash can log into the panel', function () {
    $salt = '$2a$12$'.substr(strtr(base64_encode(random_bytes(16)), '+', '.'), 0, 22);
    $hash = crypt('secret123', $salt);

    $user = User::factory()->create();

    // Write the legacy hash directly, bypassing the model's "hashed" cast.
    DB::table('users')->where('id', $user->id)->update(['password' => $hash]);

    Livewire::test(Login::class)
        ->fillForm([
            'email' => $user->email,
            'password' => 'secret123',
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors();

    $this->assertAuthenticated();
});
