<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use PDF;

class PDFController
{
    public function index(){
        $filename = 'Report - ' . Report::getTypeLabel();

        $data = [
            'title' => $filename,
        ];
        return view("");
    }
}
