<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'book_id', 'borrowed_at', 'returned_at',];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    const returnLimitDays = 15;
    const finePerDay = 0.50;

    public function daysLate(): int
    {
        if ($this->returned_at === null) {
            // Calcula a data de vencimento a partir da data de empréstimo
            $dueDate = $this->borrowed_at->addDays(self::returnLimitDays);
            $now = Carbon::now();

            if ($now->lessThanOrEqualTo($dueDate)) {
                return 0;
            }

            // Se a data atual é depois da data de vencimento, calcula os dias de atraso
            return $now->diffInDays($dueDate);
        }

        // Se o livro já foi devolvido, usa a data de devolução para calcular o atraso
        $dueDate = $this->borrowed_at->addDays(self::returnLimitDays);
        if ($this->returned_at->lessThanOrEqualTo($dueDate)) {
            return 0;
        }
        return $this->returned_at->diffInDays($dueDate);
    }

    public function calculateFine(): float
    {
        $daysLate = $this->daysLate();
        return $daysLate * self::finePerDay;
    }
}
