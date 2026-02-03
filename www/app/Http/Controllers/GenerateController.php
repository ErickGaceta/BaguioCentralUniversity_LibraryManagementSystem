<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GenerateController extends Controller
{
    public function index (){
        return view('pages.generate.index');
    }

    public function create () {
        return view('pages.generate.create');
    }
}