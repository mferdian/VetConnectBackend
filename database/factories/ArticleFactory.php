<?php

namespace Database\Factories;

use App\Models\Vet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vet_id' => Vet::inRandomOrder()->first()->id ?? Vet::factory(), 
            'judul' => $this->faker->sentence(6),
            'isi' => $this->faker->paragraph(5),
            'gambar' => json_encode([
                $this->faker->imageUrl(640, 480, 'animals', true),
                $this->faker->imageUrl(640, 480, 'animals', true),
            ]),
        ];
    }
}
