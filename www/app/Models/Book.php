<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
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

    protected $casts = [
        'publication_date' => 'date',
        'copies' => 'integer',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_code');
    }

    public function bookCopies()
    {
        return $this->hasMany(Copy::class, 'book_id');
    }
}
