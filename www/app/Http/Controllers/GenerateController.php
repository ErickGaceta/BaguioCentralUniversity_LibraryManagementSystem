<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GenerateController extends Controller
{
    public function index (){
        return view('layouts.pages.generate.index');
    }
}
