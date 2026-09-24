<?php

namespace Database\Factories;

use App\Models\Toko;
use Illuminate\Database\Eloquent\Factories\Factory;

class TokoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Toko::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nama_toko' => $this->faker->name,
            'alamat_toko' => $this->faker->name,
        ];
    }
}
