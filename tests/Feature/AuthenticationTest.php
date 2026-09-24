<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_allows_user_to_register_with_valid_data()
    {
        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        if ($response->status() !== 302) {
            dd($response->getContent());
        }

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'user@example.com']);
    }

    /** @test */
    public function it_prevents_registration_with_invalid_email()
    {
        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function it_allows_user_to_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret-password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret-password',
        ]);

        $response->assertRedirect('/search');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_prevents_login_with_incorrect_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret-password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /** @test */
    public function it_sends_a_password_reset_link_email()
    {
        $user = User::factory()->create();

        $response = $this->post('/password/email', [
            'email' => $user->email,
        ]);

        $response->assertStatus(302); // Redirect back with success message
        $response->assertSessionHas('status');
    }
}
