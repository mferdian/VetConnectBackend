<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use App\Models\Vet;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Vet::factory(10)->create();
        Article::factory(20)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@vetconnect.com',
            'password' => bcrypt('gugun123'),
            'is_admin' => true,
        ]);
    }
}
