<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookCopiesController extends Controller
{
    public function index (){
        return view('pages.copies.index');
    }
}