<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacultyFine extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'faculty_fines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'faculty_id',
        'copy_id',
        'amount',
        'reason',
    ];

    /**
     * Get the faculty that owns the fine.
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id', 'faculty_id');
    }

    /**
     * Get the copy that owns the fine.
     */
    public function copy()
    {
        return $this->belongsTo(Copy::class, 'copy_id', 'copy_id');
    }
}
