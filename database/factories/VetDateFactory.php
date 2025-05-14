<?php

namespace Database\Factories;

use App\Models\Vet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VetDate>
 */
class VetDateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vet_id' => Vet::factory(),
            'tanggal' => $this->faker->dateTimeBetween('now', '+2 weeks')->format('Y-m-d'),
        ];
    }
}
