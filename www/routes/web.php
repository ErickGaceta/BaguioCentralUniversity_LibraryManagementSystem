<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\BookCopiesController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\FacultiesController;
use App\Http\Controllers\ArchivesController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\FinesController;
use App\Http\Controllers\GenerateController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Books
Route::prefix('books')->name('books.')->controller(BooksController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::get('/edit', 'edit')->name('edit');
});

// Book Copies
Route::get('/copies', [BookCopiesController::class, 'index'])->name('copies.index');


// Transactions
Route::prefix('transactions')->name('transactions.')->controller(TransactionsController::class)->group(function () {
    Route::get('/borrow', 'borrow')->name('borrow.index');
    Route::get('/library', 'library')->name('library.index');
});

// Students
Route::prefix('students')->name('students.')->controller(StudentsController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::get('/edit', 'edit')->name('edit');
});

// Faculties
Route::prefix('faculty')->name('faculty.')->controller(FacultiesController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::get('/edit', 'edit')->name('edit');
});

// Archives
Route::prefix('archives')->name('archives.')->controller(ArchivesController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/books', 'archive_books')->name('books.index');
    Route::get('/transactions', 'archive_transactions')->name('transactions.index');
    Route::get('/users', 'archive_users')->name('users.index');
});

// Fines
Route::prefix('fines')->name('fines.')->controller(FinesController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/students', 'fines_students')->name('students.index');
    Route::get('/faculty', 'fines_faculties')->name('faculty.index');
});

// Generate
Route::prefix('generate')->name('generate.')->controller(GenerateController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
});