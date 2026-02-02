<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryTransaction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'library_transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_name',
        'ref_number',
    ];
}
