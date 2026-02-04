<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function index(){
        return view("pages.transactions.index");
    }
    public function library (){
        return view('pages.transactions.library.index');
    }
    public function borrow (){
        return view('pages.transactions.borrow.index');
    }
}
