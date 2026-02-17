<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFineArchive extends Model
{
    protected $table = 'student_fines_archive';

    protected $fillable = [
        'fine_id',
        'student_id',
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
