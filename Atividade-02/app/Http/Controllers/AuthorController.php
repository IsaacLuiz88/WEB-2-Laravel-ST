<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function __construct(){
        $this->middleware('auth')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }
    
    public function index()
    {
        $authors = Author::all();
        return view('authors.index', compact('authors'));
    }

    public function create()
    {
        return view('authors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:authors|max:255',
            'birth_date' => 'nullable|date',
        ]);
        
        Author::create($request->only('name', 'birth_date'));

        return redirect()->route('authors.index')->with('success', 'Author created successfully.');
    }

    public function show(Author $author)
    {
        return view('authors.show', compact('author'));
    }

    public function edit(Author $author)
    {
        return view('authors.edit', compact('author'));
    }

    public function update(Request $request, Author $author)
    {
        $request->validate([
            'name' => 'required|string|unique:authors,name,' . $author->id . '|max:255',
            'birth_date' => 'nullable|date',
        ]);

        $author->update($request->only('name', 'birth_date'));

        return redirect()->route('authors.index')->with('success', 'Author updated successfully.');
    }

    public function destroy(Author $author)
    {
        $author->delete();
        return redirect()->route('authors.index')->with('success', 'Author deleted successfully.');
    }
}