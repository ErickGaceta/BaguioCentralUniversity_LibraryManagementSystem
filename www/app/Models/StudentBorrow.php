<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentBorrow extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'student_borrows';

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
        'student_id',
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
     * Get the student that owns the borrow.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Get the copy that owns the borrow.
     */
    public function copy()
    {
        return $this->belongsTo(Copy::class, 'copy_id', 'copy_id');
    }
}
