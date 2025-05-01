<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vet>
 */
class VetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'no_telp' => $this->faker->phoneNumber(),
            'alamat' => $this->faker->address(),
            'STR' => 'STR-' . $this->faker->unique()->numerify('######'),
            'SIP' => 'SIP-' . $this->faker->unique()->numerify('######'),
            'harga' => $this->faker->numberBetween(50000, 250000),
            'jenis_kelamin' => $this->faker->boolean(), // true = laki-laki, false = perempuan
            'foto' => $this->faker->imageUrl(640, 480, 'animals', true), // contoh gambar random
            'tgl_lahir' => $this->faker->date(),
            'deskripsi' => $this->faker->paragraph(),
        ];
    }
}
