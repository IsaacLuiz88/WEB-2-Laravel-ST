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
    $dueDate = $this->borrowed_at->copy()->addDays(self::RETURN_DAYS_LIMIT);

    $comparisonDate = $this->returned_at ?? Carbon::now();

    if ($comparisonDate->lessThanOrEqualTo($dueDate)) {
        return 0;
    }

    $totalHoursLate = $comparisonDate->diffInHours($dueDate, false);
    $totalHoursLate = abs($totalHoursLate);
    $daysOverdue = ceil($totalHoursLate / 24);

    return (int) $daysOverdue;
}


    public function calculateFine(): float
    {
        $getDaysLate = $this->getDaysLate();
        // Garante que o valor da multa seja formatado corretamente para duas casas decimais
        return (float)number_format($getDaysLate * self::FINE_PER_DAY, 2, '.', '');
    }
}