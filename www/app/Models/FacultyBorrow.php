<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacultyBorrow extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'faculty_id',
        'copy_id',
        'ref_number',
        'return_ref_number', // Add this
        'date_borrowed',
        'due_date',
        'date_returned',
    ];

    protected $casts = [
        'date_borrowed' => 'datetime',
        'due_date' => 'date',
        'date_returned' => 'datetime',
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
