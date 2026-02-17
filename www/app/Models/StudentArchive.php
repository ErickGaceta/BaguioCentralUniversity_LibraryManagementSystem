<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentArchive extends Model
{
    protected $table = 'students_archive';

    protected $fillable = [
        'student_id',
        'first_name',
        'middle_name',
        'last_name',
        'department_id',
        'course_id',
        'year_level',
        'archived_at',
    ];

    protected $casts = [
        'year_level' => 'integer',
        'archived_at' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_code');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_code');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }
}
