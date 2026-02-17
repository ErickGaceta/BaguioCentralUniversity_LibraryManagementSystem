<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacultyArchive extends Model
{
    protected $table = 'faculties_archive';

    protected $fillable = [
        'faculty_id',
        'first_name',
        'middle_name',
        'last_name',
        'department_id',
        'occupation',
        'archived_at',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_code');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }
}
