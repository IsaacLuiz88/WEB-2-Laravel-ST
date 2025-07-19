<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Carbon\Carbon;

class Borrowing extends Pivot
{
    protected $table = 'borrowings';
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

    const RETURN_DAYS_LIMIT = 5;
    const FINE_PER_DAY = 0.50;

    public function getDaysLate(): int
    {
        // Calcula a data de vencimento (dueDate)
        $dueDate = $this->borrowed_at->copy()->addDays(self::RETURN_DAYS_LIMIT);

        $comparisonDate = $this->returned_at ?? Carbon::now();

        $dueDateStartOfDay = $dueDate->startOfDay();
        $comparisonDateStartOfDay = $comparisonDate->startOfDay();

        // Se a data de comparação é anterior ou no mesmo dia do vencimento, não há atraso.
        if ($comparisonDateStartOfDay->lessThanOrEqualTo($dueDateStartOfDay)) {
            return 0;
        }

        // Se chegamos aqui, significa que a data de comparação é após a data de vencimento (considerando apenas o dia).
        // Agora, calculamos a diferença em dias inteiros.
        $daysOverdue = $comparisonDateStartOfDay->diffInDays($dueDateStartOfDay);

        // Esta é a parte importante:
        // Se a data de comparação (mesmo que com startOfDay) é estritamente depois da data de vencimento
        // E diffInDays retornou 0 (o que pode acontecer se a diferença for, por exemplo, 23 horas e 59 minutos)
        // Então, consideramos que há pelo menos 1 dia de atraso, pois o prazo já foi excedido.
        if ($daysOverdue === 0 && $comparisonDate->isAfter($dueDate)) {
             return 1;
        }

        return $daysOverdue;
    }

    public function calculateFine(): float
    {
        $getDaysLate = $this->getDaysLate();
        // Garante que o valor da multa seja formatado corretamente para duas casas decimais
        return (float)number_format($getDaysLate * self::FINE_PER_DAY, 2, '.', '');
    }
}
