<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'student_id';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'first_name',
        'middle_name',
        'last_name',
        'department_id',
        'course_id',
        'year_level',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'year_level' => 'integer',
    ];

    /**
     * Get the department that owns the student.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_code');
    }

    /**
     * Get the course that owns the student.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_code');
    }

    /**
     * Get the borrows for the student.
     */
    public function borrows()
    {
        return $this->hasMany(StudentBorrow::class, 'student_id', 'student_id');
    }

    /**
     * Get the fines for the student.
     */
    public function fines()
    {
        return $this->hasMany(StudentFine::class, 'student_id', 'student_id');
    }

    /**
     * Get the student's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }
}
