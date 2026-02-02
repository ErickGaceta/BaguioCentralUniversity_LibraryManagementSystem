<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'department_code';

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
        'department_code',
        'name',
    ];

    /**
     * Get the books for the department.
     */
    public function books()
    {
        return $this->hasMany(Book::class, 'department_id', 'department_code');
    }

    /**
     * Get the courses for the department.
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'department_id', 'department_code');
    }

    /**
     * Get the faculties for the department.
     */
    public function faculties()
    {
        return $this->hasMany(Faculty::class, 'department_id', 'department_code');
    }

    /**
     * Get the students for the department.
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'department_id', 'department_code');
    }

    /**
     * Get the archived library items for the department.
     */
    public function archivedLibraryItems()
    {
        return $this->hasMany(ArchivesLibrary::class, 'department_id', 'department_code');
    }
}
