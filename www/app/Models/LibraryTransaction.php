<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryTransaction extends Model
{
    protected $fillable = [
        'transaction_name',
        'ref_number',
    ];
}
