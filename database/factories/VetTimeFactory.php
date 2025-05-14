<?php

namespace Database\Factories;

use App\Models\VetDate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VetTime>
 */
class VetTimeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate jam mulai antara 08:00 hingga 16:00
        $jamMulai = $this->faker->numberBetween(8, 16);
        $jamSelesai = $jamMulai + 1;

        return [
            'vet_date_id' => VetDate::factory(),
            'jam_mulai' => sprintf('%02d:00:00', $jamMulai),
            'jam_selesai' => sprintf('%02d:00:00', $jamSelesai),
        ];
    }
}
