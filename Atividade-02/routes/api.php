// routes/api.php

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rota de teste para o usuário autenticado da API (opcional, para Sanctum)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas da API para o recurso Book (CRUD)
// Não estamos aplicando autenticação aqui por padrão, mas é ALTAMENTE RECOMENDADO para APIs em produção.
// Se você quiser autenticação, adicione ->middleware('auth:sanctum') após o group ou em cada rota.
Route::controller(BookApiController::class)->group(function () {
    Route::get('/books', 'index'); // Listar todos os livros
    Route::post('/books', 'store'); // Criar um novo livro
    Route::get('/books/{book}', 'show'); // Mostrar detalhes de um livro específico
    Route::put('/books/{book}', 'update'); // Atualizar um livro existente
    Route::delete('/books/{book}', 'destroy'); // Excluir um livro
});