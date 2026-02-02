<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'course_code';

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
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_code',
        'department_id',
        'name',
    ];

    /**
     * Get the department that owns the course.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_code');
    }

    /**
     * Get the copies for the course.
     */
    public function copies()
    {
        return $this->hasMany(Copy::class, 'course_id', 'course_code');
    }

    /**
     * Get the students for the course.
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'course_id', 'course_code');
    }
}
