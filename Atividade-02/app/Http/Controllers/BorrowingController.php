<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Book;
use App\Models\Borrowing;
use Illuminate\Support\Facades\Auth;

class BorrowingController extends Controller
{
    const Borrowing_limit = 5;

    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('can:create,App\Models\Borrowing')->only(['create', 'store']);
        $this->middleware('can:update,borrowing')->only(['returnBook']);
        $this->middleware('can:view,user')->only(['userBorrowings']);
    }

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
        $selectedUser = User::find($request->input('user_id'));

        if(!$book){
            return redirect()->back()->with('Error', 'Book not found.');
        }
        if(!$selectedUser){
            return redirect()->back()->with('Error', 'User not found.');
        }

        if ($book->isBorrowed()) {
            return redirect()->route('books.show', $book->id)->withErrors('This book is already borrowed.');
        }
        if($selectedUser->hasPendingDebit()) {
            return redirect()->back()->with('Error', 'User ' . $selectedUser->name . ' has a pending debit of (R$ ' . number_format($selectedUser->debit, 2, ',', '.') . ') and cannot borrow new books.');
        }

        if($selectedUser->BorrowedBooksCount() >= self::Borrowing_limit) {
            return redirect()->back()->with('Error', 'User ' . $selectedUser->name . ' has reached the borrowing limit of ' . self::Borrowing_limit . ' books limit simultaneously.');
        }

        $borrowedBooksCount = $selectedUser->BorrowedBooksCount();

        if($borrowedBooksCount >= self::Borrowing_limit) {
            return redirect()->route('books.show', $book->id)->withErrors('User has reached the borrowing limit of ' . self::Borrowing_limit . ' books.');
        }

        $borrowing = Borrowing::create([
            'user_id' => $request->input('user_id'),
            'book_id' => $book->id,
            'borrowed_at' => now(),
            'returned_at' => null,
        ]);

        return redirect()->route('books.show', $borrowing->book_id)->with('success', 'Borrowing created successfully.');
    }

    public function returnBook(Borrowing $borrowing)
    {
        if ($borrowing->returned_at !== null) {
            return redirect()->back()->withErrors('This book has already been returned.');
        }

        $fineAmount = $borrowing->calculateFine();

        $borrowing->update([
            'returned_at' => now(),
        ]);

        if($fineAmount>0){
            $user = $borrowing->user;
            $user->debit += $fineAmount;
            $user->save();

            return redirect()->route('books.show', $borrowing->book_id)->with('success', 'Book returned successfully. A fine of R$ ' . number_format($fineAmount, 2, ',', '.') . ' has been added to the user\'s debit.');
        }

        return redirect()->route('books.show', $borrowing->book_id)->with('success', 'Book returned successfully.');
    }

    public function userBorrowings(User $user)
    {
        $borrowings = $user->books()->withPivot('borrowed_at', 'returned_at')->get();
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
