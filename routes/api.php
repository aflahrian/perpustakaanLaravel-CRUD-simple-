<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\ReportRentalController;
use App\Http\Controllers\ReportReturnController;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Routes for Books
Route::get('/books', [BooksController::class, 'index']); // Mengambil semua data buku
Route::get('/books/{id}', [BooksController::class, 'show']); // Mengambil data buku berdasarkan ID
Route::post('/books', [BooksController::class, 'store']); // Menyimpan buku baru
Route::put('/books/{id}', [BooksController::class, 'update']); // Memperbarui data buku berdasarkan ID
Route::delete('/books/{id}', [BooksController::class, 'destroy']); // Menghapus buku berdasarkan ID

// Routes for Customer
Route::get('/customers', [CustomerController::class, 'index']); // Mengambil semua data pelanggan
Route::get('/customers/{id}', [CustomerController::class, 'show']); // Mengambil data pelanggan berdasarkan ID
Route::post('/customers', [CustomerController::class, 'store']); // Menyimpan pelanggan baru
Route::put('/customers/{id}', [CustomerController::class, 'update']); // Memperbarui data pelanggan berdasarkan ID
Route::delete('/customers/{id}', [CustomerController::class, 'destroy']); // Menghapus pelanggan berdasarkan ID


// Routes for Rental
Route::get('/rental', [RentalController::class, 'index']); // Mengambil semua data pelanggan
Route::get('/rental/{id}', [RentalController::class, 'show']); // Mengambil data pelanggan berdasarkan ID
Route::post('/rental', [RentalController::class, 'store']); // Menyimpan pelanggan baru
Route::put('/rental/{id}', [RentalController::class, 'update']); // Memperbarui data pelanggan berdasarkan ID
Route::delete('/rental/{id}', [RentalController::class, 'destroy']); // Menghapus pelanggan berdasarkan ID


// Routes for Return
Route::get('/return', [ReturnController::class, 'index']); // Mengambil semua data pelanggan
Route::get('/return/{id}', [ReturnController::class, 'show']); // Mengambil data pelanggan berdasarkan ID
Route::post('/return', [ReturnController::class, 'store']); // Menyimpan pelanggan baru
Route::put('/return/{id}', [ReturnController::class, 'update']); // Memperbarui data pelanggan berdasarkan ID
Route::delete('/return/{id}', [ReturnController::class, 'destroy']); // Menghapus pelanggan berdasarkan ID

Route::get('/reportRental', [ReportRentalController::class, 'index']); // Mengambil semua data rental

Route::get('/reportReturn', [ReportReturnController::class, 'index']); // Mengambil semua data return