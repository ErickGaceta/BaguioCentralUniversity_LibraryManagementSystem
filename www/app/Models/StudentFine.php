<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFine extends Model
{
    protected $fillable = [
        'student_id',
        'copy_id',
        'amount',
        'reason',
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
