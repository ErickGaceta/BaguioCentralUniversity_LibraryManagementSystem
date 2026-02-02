<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivesLibrary extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'archives_library';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'publication_date' => 'date',
        'book_id' => 'integer',
        'copies' => 'integer',
    ];

    /**
     * Get the department that owns the archived library item.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_code');
    }
}
