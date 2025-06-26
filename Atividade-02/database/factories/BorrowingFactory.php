<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Borrowing>
 */
class BorrowingFactory extends Factory
{
    protected $model = Borrowing::class;

    public function definition(){
        return [
            'user_id' => User::factory(), //Create a new user for each borrowing or use an existing one
            'book_id' => Book::inRandomOrder()->first()->id, // Get a random book from the database
            'borrowed_at' => $this->faker->dateTimeBetween('-1 month', 'now'), //Date when the book was borrowed
            //'returned_at' => $this->faker->dateTimeBetween('now', '+1 month'), //Date when the book will be returned
            'returned_at' => null, // Set to null to indicate the book has not been returned yet
        ];

    }
}
