<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Book;
use App\Models\Borrowing;

class BorrowingController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request, Book $book)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        Borrowing::create([
            'user_id' => $request->input('user_id'),
            'book_id' => $book->id,
            'borrowed_at' => now(),
        ]);

        return redirect()->route('books.show')->with('success', 'Borrowing created successfully.');
    }

    public function returnBook(Borrowing $borrowing)
    {
        $borrowing->update([
            'returned_at' => now(),
        ]);

        return redirect()->route('books.show')->with('success', 'Book returned successfully.');
    }

    public function userBorrowings(User $user) {
        $borrowings = $user->books()->withPivot('borrowed_at, returned_at')->get();
        return view('users.borrowings', compact('user', 'borrowings'));
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
