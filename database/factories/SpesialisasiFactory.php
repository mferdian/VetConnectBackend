<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Spesialisasi>
 */
class SpesialisasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_hewan' => $this->faker->randomElement([
                'Sapi', 'Kucing', 'Kelinci', 'Burung', 'Hamster', 'Reptil', 'Ayam', 'Ikan'
            ]),
        ];
    }
}
