<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'birth_date'];

    /**
     * Faz o Laravel converter automaticamente birth_date em um objeto Carbon.
     */
    protected $casts = [
        'birth_date' => 'date',
    ];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
