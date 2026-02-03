<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $primaryKey = 'course_code';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'course_code',
        'department_id',
        'name',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_code');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'course_id', 'course_code');
    }

    public function copies()
    {
        return $this->hasMany(Copy::class, 'course_id', 'course_code');
    }
}
