<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserBorrowingSeeder extends Seeder
{
    public function run()
    {
        User::factory(10)->create()->each(function ($user) {
            $numberOfBorrowings = rand(1, 5);
            for ($i = 0; $i < $numberOfBorrowings; $i++) {
                $book = Book::whereDoesntHave('users', function ($query) {
                                $query->whereNull('returned_at');
                            })
                            ->inRandomOrder()
                            ->first();
                if ($book) {
                    Borrowing::factory()->create([
                        'user_id' => $user->id,
                        'book_id' => $book->id,
                        'returned_at' => null,
                    ]);
                } else {
                    break;
                }
            }
        });
    }
}
