<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LibraryTransactionsController extends Controller
{
    public function index (){
        return view('layouts.pages.transactions.library.index');
    }
}
