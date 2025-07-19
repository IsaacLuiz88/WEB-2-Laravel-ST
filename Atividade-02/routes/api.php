<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookApiController;
use App\Http\Controllers\Api\AuthApiController;
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
Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/logout', [AuthApiController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/register', [AuthApiController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(BookApiController::class)->group(function () {
        Route::post('/books', 'store');
        Route::put('/books/{book}', 'update');
        Route::delete('/books/{book}', 'destroy');
    });
});

Route::controller(BookApiController::class)->group(function () {
    Route::get('/books', 'index');
    Route::get('/books/{book}', 'show');
});