<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\BookCopiesController;
use App\Http\Controllers\LibraryTransactionsController;
use App\Http\Controllers\BorrowTransactionsController;
use App\Http\Controllers\FacultiesController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\GenerateController;
use App\Http\Controllers\ArchivesController;
use App\Http\Controllers\DashboardController;


Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/books', [BooksController::class, 'index'])->name('books');
Route::get('/copies', [BookCopiesController::class, 'index'])->name('copies');

Route::get('/library', [LibraryTransactionsController::class, 'index'])->name('transactions.library');
Route::get('/borrow', [BorrowTransactionsController::class, 'index'])->name('transactions.borrow');

Route::get('/students', [StudentsController::class, 'index'])->name('students');
Route::get('/faculties', [FacultiesController::class, 'index'])->name('faculties');

Route::get('/generate', [GenerateController::class, 'index'])->name('generate');
Route::get('/archives', [ArchivesController::class, 'index'])->name('archives');