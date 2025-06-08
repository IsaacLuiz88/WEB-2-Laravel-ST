<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Fiction'],
            ['name' => 'Non-Fiction'],
            ['name' => 'Fantasy'],
            ['name' => 'Science'],
            ['name' => 'Biography'],
            ['name' => 'History'],
            ['name' => 'Technology'],
            ['name' => 'Art'],
            ['name' => 'Cooking'],
            ['name' => 'Travel'],
        ];
        foreach ($categories as $category) {
            Category::create($category);
        }
        //Command to run the seeder: php artisan db:seed --class=CategorySeeder
    }
}
