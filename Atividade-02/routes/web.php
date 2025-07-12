<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('bewelcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// --- ROTAS ESPECÍFICAS RELACIONADAS A USUÁRIOS ---
Route::middleware(['auth'])->group(function () {
    // Rota para listar usuários com débitos
    Route::get('/users/debits', [UserController::class, 'listDebtors'])->name('users.debit_list');
    // Rota para zerar o débito de um usuário específico
    Route::patch('/users/{user}/clear-debit', [UserController::class, 'clearDebit'])->name('users.clear_debit');
    // Rotas de edição de papel, que também usam parâmetro {user}, mas são mais específicas que o resource show
    Route::get('users/{user}/edit-role', [UserController::class, 'editRole'])->name('users.edit_role');
    Route::put('users/{user}/update-role', [UserController::class, 'updateRole'])->name('users.update_role');
    // Rota para visualização de empréstimos de um usuário específico
    Route::get('/users/{user}/borrowings', [BorrowingController::class, 'userBorrowings'])->name('users.borrowings');
});

// --- ROTAS DE RECURSO E OUTRAS ROTAS MAIS GENÉRICAS ---
Route::resource('categories', CategoryController::class);
Route::resource('publishers', PublisherController::class);
Route::resource('authors', AuthorController::class);

// Rotas específicas de criação de livros
Route::get('/books/create-id-number', [BookController::class, 'createWithId'])->name('books.create.id');
Route::post('/books/create-id-number', [BookController::class, 'storeWithId'])->name('books.store.id');
Route::get('/books/create-select', [BookController::class, 'createWithSelect'])->name('books.create.select');
Route::post('/books/create-select', [BookController::class, 'storeWithSelect'])->name('books.store.select');

// Rotas RESTful para livros (excluindo create e store que foram customizadas)
Route::resource('books', BookController::class)->except(['create', 'store']);

// Rotas RESTful para usuários (excluindo create, store, destroy, pois algumas são customizadas ou você as excluiu intencionalmente)
// Esta rota resource agora vem DEPOIS das rotas específicas de users.
Route::resource('users', UserController::class)->except(['create', 'store', 'destroy']);


// Rotas de Empréstimos (Borrowing)
Route::post('/borrow/{book}/borrow', [BorrowingController::class, 'store'])->name('books.borrow');
Route::patch('/borrowings/{borrowing}/return', [BorrowingController::class, 'returnBook'])->name('borrowings.return');

// Rota de atualização de capa de livro
Route::put('/books/{book}/cover', [BookController::class, 'updateCover'])->name('books.updateCover');