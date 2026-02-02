<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'faculty_id';

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
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'faculties';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'faculty_id',
        'first_name',
        'middle_name',
        'last_name',
        'department_id',
        'occupation',
    ];

    /**
     * Get the department that owns the faculty.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_code');
    }

    /**
     * Get the borrows for the faculty.
     */
    public function borrows()
    {
        return $this->hasMany(FacultyBorrow::class, 'faculty_id', 'faculty_id');
    }

    /**
     * Get the fines for the faculty.
     */
    public function fines()
    {
        return $this->hasMany(FacultyFine::class, 'faculty_id', 'faculty_id');
    }

    /**
     * Get the faculty's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }
}
