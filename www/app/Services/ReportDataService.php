<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportDataService
{
    public function collect(string $type, Carbon $from, Carbon $to): array
    {
        $data = match ($type) {
            'issuance'      => $this->issuance($from, $to),
            'book_added'    => $this->bookAdded($from, $to),
            'book_archived' => $this->bookArchived($from, $to),
            'fines_student' => $this->finesStudent($from, $to),
            'fines_faculty' => $this->finesFaculty($from, $to),
            'fines_both'    => $this->finesBoth($from, $to),
            default         => [],
        };

        return [$data, count($data)];
    }

    private function issuance(Carbon $from, Carbon $to): array
    {
        $studentRows = DB::table('student_borrows')
            ->join('students', 'student_borrows.student_id', '=', 'students.student_id')
            ->join('copies',   'student_borrows.copy_id',    '=', 'copies.copy_id')
            ->join('books',    'copies.book_id',             '=', 'books.id')
            ->whereBetween('student_borrows.date_borrowed', [$from, $to])
            ->select(
                'student_borrows.ref_number',
                DB::raw("'Student' as borrower_type"),
                DB::raw("students.first_name || ' ' || students.last_name as borrower_name"),
                'books.title as book_title',
                'student_borrows.date_borrowed',
                'student_borrows.due_date',
                'student_borrows.date_returned',
            )
            ->get()->toArray();

        $facultyRows = DB::table('faculty_borrows')
            ->join('faculties', 'faculty_borrows.faculty_id', '=', 'faculties.faculty_id')
            ->join('copies',    'faculty_borrows.copy_id',    '=', 'copies.copy_id')
            ->join('books',     'copies.book_id',             '=', 'books.id')
            ->whereBetween('faculty_borrows.date_borrowed', [$from, $to])
            ->select(
                'faculty_borrows.ref_number',
                DB::raw("'Faculty' as borrower_type"),
                DB::raw("faculties.first_name || ' ' || faculties.last_name as borrower_name"),
                'books.title as book_title',
                'faculty_borrows.date_borrowed',
                'faculty_borrows.due_date',
                'faculty_borrows.date_returned',
            )
            ->get()->toArray();

        return array_merge($studentRows, $facultyRows);
    }

    private function bookAdded(Carbon $from, Carbon $to): array
    {
        return DB::table('books')
            ->whereBetween('created_at', [$from, $to])
            ->select('id', 'title', 'author', 'isbn', 'publisher', 'copies', 'created_at')
            ->get()->toArray();
    }

    private function bookArchived(Carbon $from, Carbon $to): array
    {
        return DB::table('archives_library')
            ->whereBetween('created_at', [$from, $to])
            ->select('id', 'title', 'author', 'isbn', 'publisher', 'copies', 'created_at')
            ->get()->toArray();
    }

    private function finesStudent(Carbon $from, Carbon $to): array
    {
        return DB::table('student_fines')
            ->join('students', 'student_fines.student_id', '=', 'students.student_id')
            ->join('copies',   'student_fines.copy_id',    '=', 'copies.copy_id')
            ->join('books',    'copies.book_id',           '=', 'books.id')
            ->whereBetween('student_fines.created_at', [$from, $to])
            ->select(
                'student_fines.id',
                DB::raw("students.first_name || ' ' || students.last_name as name"),
                'books.title as book_title',
                'student_fines.amount',
                'student_fines.reason',
                'student_fines.status',
                'student_fines.date_paid',
            )
            ->get()->toArray();
    }

    private function finesFaculty(Carbon $from, Carbon $to): array
    {
        return DB::table('faculty_fines')
            ->join('faculties', 'faculty_fines.faculty_id', '=', 'faculties.faculty_id')
            ->join('copies',    'faculty_fines.copy_id',    '=', 'copies.copy_id')
            ->join('books',     'copies.book_id',           '=', 'books.id')
            ->whereBetween('faculty_fines.created_at', [$from, $to])
            ->select(
                'faculty_fines.id',
                DB::raw("faculties.first_name || ' ' || faculties.last_name as name"),
                'books.title as book_title',
                'faculty_fines.amount',
                'faculty_fines.reason',
                'faculty_fines.status',
                'faculty_fines.date_paid',
            )
            ->get()->toArray();
    }

    private function finesBoth(Carbon $from, Carbon $to): array
    {
        $student = DB::table('student_fines')
            ->join('students', 'student_fines.student_id', '=', 'students.student_id')
            ->join('copies',   'student_fines.copy_id',    '=', 'copies.copy_id')
            ->join('books',    'copies.book_id',           '=', 'books.id')
            ->whereBetween('student_fines.created_at', [$from, $to])
            ->select(
                'student_fines.id',
                DB::raw("'Student' as borrower_type"),
                DB::raw("students.first_name || ' ' || students.last_name as name"),
                'books.title as book_title',
                'student_fines.amount',
                'student_fines.reason',
                'student_fines.status',
                'student_fines.date_paid',
            )
            ->get()->toArray();

        $faculty = DB::table('faculty_fines')
            ->join('faculties', 'faculty_fines.faculty_id', '=', 'faculties.faculty_id')
            ->join('copies',    'faculty_fines.copy_id',    '=', 'copies.copy_id')
            ->join('books',     'copies.book_id',           '=', 'books.id')
            ->whereBetween('faculty_fines.created_at', [$from, $to])
            ->select(
                'faculty_fines.id',
                DB::raw("'Faculty' as borrower_type"),
                DB::raw("faculties.first_name || ' ' || faculties.last_name as name"),
                'books.title as book_title',
                'faculty_fines.amount',
                'faculty_fines.reason',
                'faculty_fines.status',
                'faculty_fines.date_paid',
            )
            ->get()->toArray();

        return array_merge($student, $faculty);
    }
}
