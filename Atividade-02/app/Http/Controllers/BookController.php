<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{

    public function index()
    {
       /*  $books = Book::all();
        return view('books.create-id', compact('books')); */
        $books = Book::with('author')->paginate(20);
        return view('books.index', compact('books'));
    }

    public function createWithId()
    {
        return view('books.create-id');
    }

    public function storeWithId(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $data = $request->all();

        if($request->hasFile('cover_image')){
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        Book::create($data);
        return redirect()->route('books.index')->with('success', 'Book created successfully.');
    }

    public function createWithSelect()
    {
        $publishers = Publisher::all();
        $authors = Author::all();
        $categories = Category::all();
        return view('books.create-select', compact('publishers', 'authors', 'categories'));
    }

    public function storeWithSelect(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();

        if($request->hasFile('cover_image')){
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }
        Book::create($data);
        return redirect()->route('books.index')->with('success', 'Book created successfully.');
    }

    public function show(Book $book)
    {
        $book->load(['author', 'category', 'publisher']);
        $users = User::all();
        return view('books.show', compact('book', 'users'));
    }

    public function edit(Book $book)
    {
        $publishers = Publisher::all();
        $authors = Author::all();
        $categories = Category::all();

        return view('books.edit', compact('book', 'publishers', 'authors', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
         $data = $request->all();

    if ($request->hasFile('cover_image')) {
        // Deleta a imagem antiga
        if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
            Storage::disk('public')->delete($book->cover_image);
        }

        // Salva a nova
        $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
    }
        $book->update($request->all());

        return redirect()->route('books.index')->with('success', 'Book updated successfully.');
    }

    public function updateCoverImage(Request $request, Book $book){
        $request->validate([
        'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',]);

        if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->update([
            'cover_image' => $request->file('cover_image')->store('covers', 'public'),
        ]);

        return redirect()->route('books.edit', $book)->with('success', 'Cover image updated successfully.');
    }

    public function destroy(Book $book)
    {
        if($book->cover_image && Storage::disk('public')->exists($book->cover_image)){
            Storage::disk('public')->delete($book->cover_image);
        }
        $book->delete();
        return redirect()->route('books.index')->with('success', 'Book deleted successfully.');
    }
}