<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacultyFine extends Model
{
    protected $fillable = [
        'faculty_id',
        'copy_id',
        'amount',
        'reason',
        'status',
        'date_paid',
    ];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id', 'faculty_id');
    }

    public function copy()
    {
        return $this->belongsTo(Copy::class, 'copy_id', 'copy_id');
    }

    public function isPaid()
    {
        return $this->status === 1;
    }

    public function markAsPaid()
    {
        $this->status = 1;
        $this->date_paid = now();
        $this->save();
    }
}
