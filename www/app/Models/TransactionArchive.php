<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionArchive extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaction_archives';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_borrow_transaction_id',
        'faculty_borrow_transaction_id',
        'library_transaction_id',
        'name',
    ];
}
