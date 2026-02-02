<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Copy extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'copy_id';

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
        'copy_id',
        'book_id',
        'course_id',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'book_id' => 'integer',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'available',
    ];

    /**
     * Get the book that owns the copy.
     */
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    /**
     * Get the course that owns the copy.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_code');
    }

    /**
     * Get the student borrows for the copy.
     */
    public function studentBorrows()
    {
        return $this->hasMany(StudentBorrow::class, 'copy_id', 'copy_id');
    }

    /**
     * Get the faculty borrows for the copy.
     */
    public function facultyBorrows()
    {
        return $this->hasMany(FacultyBorrow::class, 'copy_id', 'copy_id');
    }

    /**
     * Get the student fines for the copy.
     */
    public function studentFines()
    {
        return $this->hasMany(StudentFine::class, 'copy_id', 'copy_id');
    }

    /**
     * Get the faculty fines for the copy.
     */
    public function facultyFines()
    {
        return $this->hasMany(FacultyFine::class, 'copy_id', 'copy_id');
    }
}
