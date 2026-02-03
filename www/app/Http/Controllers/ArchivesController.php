<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArchivesController extends Controller
{
    public function index (){
        return view('pages.archives.index');
    }

    public function archive_books (){
        return view('pages.archives.books.index');
    }

    public function archive_library (){
        return view('pages.archives.library.index');
    }

    public function archive_users (){
        return view('pages.archives.users.index');
    }
}