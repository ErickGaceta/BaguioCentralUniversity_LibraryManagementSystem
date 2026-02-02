<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Copy;
use App\Models\Student;
use App\Models\Faculty;


class DashboardController extends Controller
{
    public function index (){
        $totalBooks = Book::count();
        $totalCopies = Copy::count();
        $totalStudents = Student::count();
        $totalFaculties = Faculty::count();
        return view('dashboard', compact('totalBooks', 'totalCopies', 'totalStudents', 'totalFaculties'));
    }
}
