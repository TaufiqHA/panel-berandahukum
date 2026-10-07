<?php

namespace Database\Factories;

use App\Models\WhitelistedIp;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WhitelistedIp>
 */
class WhitelistedIpFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ip_address' => fake()->unique()->ipv4(),
            'keterangan' => fake()->optional()->sentence(3),
        ];
    }
}
