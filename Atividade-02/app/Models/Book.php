<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'author_id', 'category_id', 'publisher_id', 'published_year', 'cover_image'];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function users(){
        return $this->belongsToMany(User::class, 'borrowings')
        ->withPivot('id', 'borrowed_at', 'returned_at')
        ->withTimestamps();
    }

    public function isBorrowed(): bool
    {
        // Busca na tabela pivô 'borrowings' por um registro onde:
        // 1. O 'book_id' seja o ID deste livro
        // 2. A coluna 'returned_at' seja NULL (indicando empréstimo em aberto)
        return $this->users()->wherePivotNull('returned_at')->exists();
    }

    public function currentBorrowing(){
        return $this->hasMany(Borrowing::class)->whereNull('returned_at')
            ->latest()
            ->first();
    }
}
