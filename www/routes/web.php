<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\Pages\Books\BookIndex;
use App\Livewire\Pages\Books\BookEdit;
use App\Livewire\Pages\Books\BookCreate;
use App\Livewire\Pages\Copies\CopyIndex;
use App\Livewire\Pages\Transactions\Issuance;
use App\Livewire\Pages\Transactions\Library;
use App\Livewire\Pages\Users\FacultyCreate;
use App\Livewire\Pages\Users\FacultyEdit;
use App\Livewire\Pages\Users\FacultyIndex;
use App\Livewire\Pages\Users\StudentCreate;
use App\Livewire\Pages\Users\StudentEdit;
use App\Livewire\Pages\Users\StudentIndex;
use App\Livewire\Pages\Fines\Student;
use App\Livewire\Pages\Fines\Faculty;
use App\Livewire\Pages\Archives\Libraries;
use App\Livewire\Pages\Archives\Users;
use App\Livewire\Pages\Archives\Transaction;
use App\Livewire\Pages\Reports\Generate;
use App\Livewire\Pages\Reports\Index;

Route::get('/', Dashboard::class)->name('dashboard');


Route::prefix('books')->name('books.')->group(function () {
    Route::get('/', BookIndex::class)->name('index');
});

Route::get('/copies', CopyIndex::class)->name('copies.index');

Route::prefix('transactions')->name('transactions.')->group(function () {
    Route::get('/issuance', Issuance::class)->name('issuance');
    Route::get('/library', Library::class)->name('library');
});

Route::prefix('users')->name('users.')->group(function () {
    Route::get('/students-index', StudentIndex::class)->name('students-index');
    Route::get('/students-create', StudentCreate::class)->name('students-create');
    Route::get('/students-edit', StudentEdit::class)->name('students-edit');
    Route::get('/faculties-index', FacultyIndex::class)->name('faculties-index');
    Route::get('/faculties-create', FacultyCreate::class)->name('faculties-create');
    Route::get('/faculties-edit', FacultyEdit::class)->name('faculties-edit');
});

Route::prefix('fines')->name('fines.')->group(function () {
    Route::get('/student-fines', Student::class)->name('student-fines');
    Route::get('/faculty-fines', Faculty::class)->name('faculty-fines');
});

Route::prefix('archives')->name('archives.')->group(function () {
    Route::get('/archives-library', Libraries::class)->name('archives-library');
    Route::get('/archives-transactions', Transaction::class)->name('archives-transactions');
    Route::get('/archives-users', Users::class)->name('archives-users');
});

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', Index::class)->name('reports-index');
    Route::get('/generate', Generate::class)->name('reports-generate');
});
