<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Copy extends Model
{
    protected $primaryKey = 'copy_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'copy_id',
        'book_id',
        'course_id',
        'status',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_code');
    }

    public function studentBorrows()
    {
        return $this->hasMany(StudentBorrow::class, 'copy_id', 'copy_id');
    }

    public function facultyBorrows()
    {
        return $this->hasMany(FacultyBorrow::class, 'copy_id', 'copy_id');
    }

    public function studentFines()
    {
        return $this->hasMany(StudentFine::class, 'copy_id', 'copy_id');
    }

    public function facultyFines()
    {
        return $this->hasMany(FacultyFine::class, 'copy_id', 'copy_id');
    }
}
