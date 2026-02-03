<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FacultiesController extends Controller
{
    public function index (){
        return view('pages.faculty.index');
    }

    public function create () {
        return view('pages.faculty.create');
    }

    public function edit () {
        return view('pages.faculty.edit');
    }
}
