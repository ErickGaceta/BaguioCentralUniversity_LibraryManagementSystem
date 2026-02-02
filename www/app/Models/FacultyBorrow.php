<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacultyBorrow extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'faculty_borrows';

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
        'faculty_id',
        'copy_id',
        'ref_number',
        'date_borrowed',
        'due_date',
        'date_returned',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_borrowed' => 'date',
        'due_date' => 'date',
        'date_returned' => 'date',
    ];

    /**
     * Get the faculty that owns the borrow.
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id', 'faculty_id');
    }

    /**
     * Get the copy that owns the borrow.
     */
    public function copy()
    {
        return $this->belongsTo(Copy::class, 'copy_id', 'copy_id');
    }
}
