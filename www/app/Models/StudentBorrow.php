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
        'date_borrowed',
        'due_date',
        'date_returned',
    ];

    protected $casts = [
        'date_borrowed' => 'date',
        'due_date' => 'date',
        'date_returned' => 'date',
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
