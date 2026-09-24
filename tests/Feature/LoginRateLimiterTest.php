<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\BlockedIp;

class LoginRateLimiterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_blocks_ip_after_2_failed_attempts_on_the_3rd_attempt()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Attempt 1: Failed
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);
        $response->assertSessionHasErrors('email');
        $this->assertCount(0, BlockedIp::all());

        // Attempt 2: Failed
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);
        $response->assertSessionHasErrors('email');
        $this->assertCount(0, BlockedIp::all());

        // Attempt 3: Locked out
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        // It should redirect back with the "too many attempts" message
        $response->assertSessionHasErrors('email');
        // The exact message might depend on localization, but let's check for "attempts"
        $this->assertStringContainsString('attempts', session('errors')->first('email'));
        
        // Check if IP is blocked in database
        $this->assertCount(1, BlockedIp::all());
    }
}
