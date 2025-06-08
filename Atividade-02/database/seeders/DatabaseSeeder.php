<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Faker\Factory as FakerFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        FakerFactory::create()->unique(true);
        $this->call([CategorySeeder::class, AuthorPublisherBookSeeder::class, UserBorrowingSeeder::class]);
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'name' => 'ii',
            'email' => 'i@i.i',
            'password' => 'iiiiiiii',
        ]);
    }
}
//command laravel to drop tables: 
