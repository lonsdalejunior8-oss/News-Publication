<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@sig.local',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Author User',
            'email' => 'author@sig.local',
            'role' => 'author',
        ]);

        foreach (['General', 'Press Releases', 'Announcements'] as $name) {
            Category::create(['name' => $name, 'slug' => \Illuminate\Support\Str::slug($name)]);
        }
    }
}
