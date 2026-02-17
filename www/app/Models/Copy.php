<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Copy extends Model
{
    protected $primaryKey = 'copy_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'copy_id',
        'book_id',
        'course_id',
        'status',
        'condition',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_code');
    }

    public function studentBorrows()
    {
        return $this->hasMany(StudentBorrow::class, 'copy_id', 'copy_id');
    }

    public function facultyBorrows()
    {
        return $this->hasMany(FacultyBorrow::class, 'copy_id', 'copy_id');
    }

    public function studentFines()
    {
        return $this->hasMany(StudentFine::class, 'copy_id', 'copy_id');
    }

    public function facultyFines()
    {
        return $this->hasMany(FacultyFine::class, 'copy_id', 'copy_id');
    }

    /**
     * Borrow this copy for a student
     */
    public function borrowByStudent(string $studentId, string $refNumber, string $dueDate): void
    {
        if ($this->status !== 'Available') {
            throw new \LogicException('This copy is not available for borrowing.');
        }

        StudentBorrow::create([
            'student_id' => $studentId,
            'copy_id' => $this->copy_id,
            'ref_number' => $refNumber,
            'return_ref_number' => null,
            'date_borrowed' => Carbon::now(),
            'due_date' => $dueDate,
            'date_returned' => null,
        ]);

        $this->update(['status' => 'Borrowed']);
    }

    /**
     * Borrow this copy for a faculty
     */
    public function borrowByFaculty(string $facultyId, string $refNumber, string $dueDate): void
    {
        if ($this->status !== 'Available') {
            throw new \LogicException('This copy is not available for borrowing.');
        }

        FacultyBorrow::create([
            'faculty_id' => $facultyId,
            'copy_id' => $this->copy_id,
            'ref_number' => $refNumber,
            'return_ref_number' => null,
            'date_borrowed' => Carbon::now(),
            'due_date' => $dueDate,
            'date_returned' => null,
        ]);

        $this->update(['status' => 'Borrowed']);
    }

    /**
     * Return this copy with automatic damage fine processing
     */
    public function returnCopy(string $condition, string $returnRefNumber, string $type = 'student'): void
    {
        if ($this->status !== 'Borrowed') {
            throw new \LogicException('This copy is not currently borrowed.');
        }

        $today = Carbon::now()->format('Y-m-d H:i:s');

        if ($type === 'student') {
            $activeBorrow = StudentBorrow::where('copy_id', $this->copy_id)
                ->whereNull('date_returned')
                ->first();

            if (!$activeBorrow) {
                throw new \LogicException('No active student borrow found for this copy.');
            }

            // Update borrow record
            $activeBorrow->date_returned = $today;
            $activeBorrow->return_ref_number = $returnRefNumber;
            $activeBorrow->save();

            // Process damage fine if condition changed
            $this->processDamageFine($activeBorrow->student_id, $condition, 'student');
        } else {
            $activeBorrow = FacultyBorrow::where('copy_id', $this->copy_id)
                ->whereNull('date_returned')
                ->first();

            if (!$activeBorrow) {
                throw new \LogicException('No active faculty borrow found for this copy.');
            }

            // Update borrow record
            $activeBorrow->date_returned = $today;
            $activeBorrow->return_ref_number = $returnRefNumber;
            $activeBorrow->save();

            // Process damage fine if condition changed
            $this->processDamageFine($activeBorrow->faculty_id, $condition, 'faculty');
        }

        // Update copy status and condition
        $this->update([
            'status' => 'Available',
            'condition' => $condition,
        ]);
    }

    /**
     * Process damage fine based on condition
     */
    private function processDamageFine(string $userId, string $newCondition, string $type): void
    {
        // Get damage fine amount based on condition
        $damageAmount = $this->getDamageFineAmount($newCondition);

        if ($damageAmount > 0) {
            $reason = $this->getDamageReason($newCondition);

            if ($type === 'student') {
                StudentFine::create([
                    'student_id' => $userId,
                    'copy_id' => $this->copy_id,
                    'amount' => (string) $damageAmount,
                    'reason' => $reason,
                    'status' => 0,
                    'date_paid' => null,
                ]);
            } else {
                FacultyFine::create([
                    'faculty_id' => $userId,
                    'copy_id' => $this->copy_id,
                    'amount' => (string) $damageAmount,
                    'reason' => $reason,
                    'status' => 0,
                    'date_paid' => null,
                ]);
            }
        }
    }

    /**
     * Get fine amount based on damage condition
     */
    private function getDamageFineAmount(string $condition): float
    {
        return match (strtolower($condition)) {
            'minor damage' => config('library.damage_fines.minor', 50.00),
            'major damage' => config('library.damage_fines.major', 150.00),
            'total damage', 'lost' => config('library.damage_fines.total', 500.00),
            default => 0.00,
        };
    }

    /**
     * Get damage reason text
     */
    private function getDamageReason(string $condition): string
    {
        return match (strtolower($condition)) {
            'minor damage' => 'Minor Damage',
            'major damage' => 'Major Damage',
            'total damage' => 'Total Damage',
            'lost' => 'Lost Book',
            default => 'Damage',
        };
    }

    // Helper methods
    public function isAvailable()
    {
        return $this->status === 'Available';
    }

    public function isBorrowed()
    {
        return $this->status === 'Borrowed';
    }
}
