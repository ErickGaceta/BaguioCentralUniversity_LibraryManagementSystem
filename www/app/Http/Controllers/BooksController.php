<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BooksController extends Controller
{
    public function index (){
        return view('pages.books.index');
    }

    public function create () {
        return view('pages.books.create');
    }

    public function edit () {
        return view('pages.books.edit');
    }
}