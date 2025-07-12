<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Borrowing>
 */
class BorrowingFactory extends Factory
{
    protected $model = Borrowing::class;

    public function definition(): array
    {
        $borrowedAt = $this->faker->dateTimeBetween('-2 months', '-10 days'); // Pega data de empréstimo até 2 meses atrás
        $returnedAt = null;

        // 50% de chance de o livro estar devolvido, 50% em aberto
        if ($this->faker->boolean(50)) {
            // Se devolvido, calcula uma data de retorno que pode ser atrasada
            $dueDate = Carbon::parse($borrowedAt)->addDays(Borrowing::returnLimitDays);
            // 60% de chance de ser devolvido no prazo ou antes, 40% de chance de ser atrasado
            if ($this->faker->boolean(60)) {
                $returnedAt = $this->faker->dateTimeBetween($borrowedAt, $dueDate->toDateTimeString());
            } else {
                $returnedAt = $this->faker->dateTimeBetween($dueDate->addDay()->toDateTimeString(), 'now');
            }
        }

        return [
            'user_id' => User::factory(),
            'book_id' => Book::inRandomOrder()->first()->id,
            'borrowed_at' => $borrowedAt,
            'returned_at' => $returnedAt,
        ];
    }

    // Estado para criar um empréstimo em aberto
    public function inOpen()
    {
        return $this->state(fn(array $attributes) => [
            'returned_at' => null,
        ]);
    }

    // Estado para criar um empréstimo devolvido com atraso
    public function returnedLate()
    {
        return $this->state(function (array $attributes) {
            $borrowedAt = Carbon::parse($attributes['borrowed_at']);
            $dueDate = $borrowedAt->addDays(Borrowing::returnLimitDays);
            return [
                'returned_at' => $this->faker->dateTimeBetween($dueDate->addDay()->toDateTimeString(), 'now'),
            ];
        });
    }

    // Estado para criar um empréstimo devolvido no prazo
    public function returnedOnTime()
    {
        return $this->state(function (array $attributes) {
            $borrowedAt = Carbon::parse($attributes['borrowed_at']);
            $dueDate = $borrowedAt->addDays(Borrowing::returnLimitDays);
            return [
                'returned_at' => $this->faker->dateTimeBetween($borrowedAt->toDateTimeString(), $dueDate->toDateTimeString()),
            ];
        });
    }
}
    /*public function definition(){
        return [
            'user_id' => User::factory(), //Create a new user for each borrowing or use an existing one
            'book_id' => Book::inRandomOrder()->first()->id, // Get a random book from the database
            'borrowed_at' => $this->faker->dateTimeBetween('-1 month', 'now'), //Date when the book was borrowed
            //'returned_at' => $this->faker->dateTimeBetween('now', '+1 month'), //Date when the book will be returned
            'returned_at' => null, // Set to null to indicate the book has not been returned yet
        ];

    }*/
