<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $primaryKey = 'student_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'student_id',
        'first_name',
        'middle_name',
        'last_name',
        'department_id',
        'course_id',
        'year_level',
    ];

    protected $casts = [
        'year_level' => 'integer',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_code');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_code');
    }

    public function borrows()
    {
        return $this->hasMany(StudentBorrow::class, 'student_id', 'student_id');
    }

    public function fines()
    {
        return $this->hasMany(StudentFine::class, 'student_id', 'student_id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }
}
