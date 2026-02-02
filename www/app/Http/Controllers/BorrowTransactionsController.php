<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BorrowTransactionsController extends Controller
{
    public function index (){
        return view('layouts.pages.transactions.borrow.index');
    }
}
