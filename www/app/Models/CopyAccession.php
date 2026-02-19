<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CopyAccession extends Model
{
    protected $fillable = [
        'copy_id',
        'accession_number',
        'call_number',
    ];

    public function copy()
    {
        return $this->belongsTo(Copy::class, 'copy_id', 'copy_id');
    }
}
