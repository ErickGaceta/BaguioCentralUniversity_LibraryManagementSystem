<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionArchive extends Model
{
    protected $fillable = [
        'student_borrow_transaction_id',
        'faculty_borrow_transaction_id',
        'library_transaction_id',
        'name',
    ];
}
