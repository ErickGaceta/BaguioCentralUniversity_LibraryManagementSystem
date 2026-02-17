<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacultyFineArchive extends Model
{
    protected $table = 'faculty_fines_archive';

    protected $fillable = [
        'fine_id',
        'faculty_id',
        'copy_id',
        'amount',
        'reason',
        'status',
        'date_paid',
        'archived_at',
    ];

    protected $casts = [
        'date_paid' => 'date',
        'archived_at' => 'datetime',
        'status' => 'integer',
    ];
}
