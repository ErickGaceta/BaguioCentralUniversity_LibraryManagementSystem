<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacultyFine extends Model
{
    protected $fillable = [
        'faculty_id',
        'copy_id',
        'amount',
        'reason',
    ];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id', 'faculty_id');
    }

    public function copy()
    {
        return $this->belongsTo(Copy::class, 'copy_id', 'copy_id');
    }
}
