<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $table = 'faculties';
    protected $primaryKey = 'faculty_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'faculty_id',
        'first_name',
        'middle_name',
        'last_name',
        'department_id',
        'occupation',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_code');
    }

    public function borrows()
    {
        return $this->hasMany(FacultyBorrow::class, 'faculty_id', 'faculty_id');
    }

    public function fines()
    {
        return $this->hasMany(FacultyFine::class, 'faculty_id', 'faculty_id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }
}
