<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArchivesCopy extends Model
{
    protected $fillable = [
        'archived_book_id',
        'original_book_id',
        'copy_id',
        'course_id',
        'status',
        'condition',
    ];

    public function archivedBook(): BelongsTo
    {
        return $this->belongsTo(ArchivesLibrary::class, 'archived_book_id');
    }
}
