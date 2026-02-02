<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FacultiesController extends Controller
{
    public function index (){
        return view('layouts.pages.users.faculties.index');
    }
}
