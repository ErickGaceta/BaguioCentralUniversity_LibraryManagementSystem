<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'author',
        'publication_date',
        'publisher',
        'isbn',
        'department_id',
        'category',
        'copies',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'publication_date' => 'date',
        'copies' => 'integer',
    ];

    /**
     * Get the department that owns the book.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_code');
    }

    /**
     * Get the copies for the book.
     */
    public function bookCopies()
    {
        return $this->hasMany(Copy::class, 'book_id');
    }
}
