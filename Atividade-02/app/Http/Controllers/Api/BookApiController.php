<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Support\Facades\Validator;

class BookApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::with(['author', 'category', 'publisher'])->paginate(10); // Paginação para APIs grandes
        
        return response()->json([
            'message' => 'Livros listados com sucesso.',
            'data' => $books->items(), // Pega os itens da coleção paginada
            'meta' => [ // Informações de paginação
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
            ]
        ], 200);
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'publisher_id' => 'required|exists:publishers,id',
            'published_year' => 'nullable|integer|min:1000|max:' . date('Y'), // Ano de publicação
            'cover_image' => 'nullable|string|max:255', // Para API, pode ser uma URL ou caminho
        ]);

        // Se a validação falhar, retorna erro 422 (Unprocessable Entity)
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cria o novo livro
        $book = Book::create($request->all());

        // Retorna o livro criado com status 201 (Created)
        return response()->json([
            'message' => 'Livro criado com sucesso!',
            'data' => $book->load(['author', 'category', 'publisher']) // Carrega relacionamentos para o retorno
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        return response()->json([
            'message' => 'Detalhes do livro.',
            'data' => $book->load(['author', 'category', 'publisher'])
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'author_id' => 'sometimes|required|exists:authors,id',
            'category_id' => 'sometimes|required|exists:categories,id',
            'publisher_id' => 'sometimes|required|exists:publishers,id',
            'published_year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'cover_image' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Atualiza o livro com os dados da requisição
        $book->update($request->all());

        // Retorna o livro atualizado
        return response()->json([
            'message' => 'Livro atualizado com sucesso!',
            'data' => $book->load(['author', 'category', 'publisher'])
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();

        // Retorna uma resposta vazia com status 204 (No Content)
        return response()->json([
            'message' => 'Livro excluído com sucesso!'
        ], 204);
    }
}
