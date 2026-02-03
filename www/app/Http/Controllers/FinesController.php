<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinesController extends Controller
{
    public function index (){
        return view('pages.fines.index');
    }
    public function fines_students (){
        return view('pages.fines.student.index');
    }
    public function fines_faculties (){
        return view('pages.fines.faculty.index');
    }
}