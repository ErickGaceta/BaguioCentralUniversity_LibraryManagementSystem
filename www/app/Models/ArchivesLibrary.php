<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivesLibrary extends Model
{
    protected $table = 'archives_library';

    protected $fillable = [
        'book_id',
        'title',
        'author',
        'publication_date',
        'publisher',
        'isbn',
        'department_id',
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
}
