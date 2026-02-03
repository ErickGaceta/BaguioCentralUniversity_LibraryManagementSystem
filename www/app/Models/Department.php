<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $primaryKey = 'department_code';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'department_code',
        'name',
    ];

    public function books()
    {
        return $this->hasMany(Book::class, 'department_id', 'department_code');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'department_id', 'department_code');
    }

    public function faculties()
    {
        return $this->hasMany(Faculty::class, 'department_id', 'department_code');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'department_id', 'department_code');
    }

    public function archivedLibraryItems()
    {
        return $this->hasMany(ArchivesLibrary::class, 'department_id', 'department_code');
    }
}
