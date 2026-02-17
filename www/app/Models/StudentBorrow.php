<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentBorrow extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'student_id',
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

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function copy()
    {
        return $this->belongsTo(Copy::class, 'copy_id', 'copy_id');
    }
}
