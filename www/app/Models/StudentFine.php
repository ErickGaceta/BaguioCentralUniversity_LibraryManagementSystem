<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFine extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'student_fines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'copy_id',
        'amount',
        'reason',
    ];

    /**
     * Get the student that owns the fine.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Get the copy that owns the fine.
     */
    public function copy()
    {
        return $this->belongsTo(Copy::class, 'copy_id', 'copy_id');
    }
}
