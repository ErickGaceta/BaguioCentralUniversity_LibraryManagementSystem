<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Copy;
use App\Models\Student;
use App\Models\Faculty;
use App\Models\StudentFine;
use App\Models\FacultyFine;
use App\Models\StudentBorrow;
use App\Models\FacultyBorrow;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBooks = Book::count();
        $totalCopies = Copy::count();
        $totalStudents = Student::count();
        $totalFaculties = Faculty::count();
        $totalFines = StudentFine::count() + FacultyFine::count();
        $totalBorrows = StudentBorrow::count() + FacultyBorrow::count();

        // Get recent transactions (last 10)
        $recentTransactions = $this->getRecentTransactions();

        return view('dashboard', compact(
            'totalBooks',
            'totalCopies',
            'totalStudents',
            'totalFaculties',
            'totalFines',
            'totalBorrows',
            'recentTransactions'
        ));
    }

    private function getRecentTransactions()
    {
        // Get student borrows
        $studentBorrows = StudentBorrow::with(['student', 'copy.book'])
            ->select('student_id as user_id', 'date_borrowed as transaction_date', 'date_returned', 'ref_number', DB::raw("'student' as user_type"))
            ->get();

        // Get faculty borrows
        $facultyBorrows = FacultyBorrow::with(['faculty', 'copy.book'])
            ->select('faculty_id as user_id', 'date_borrowed as transaction_date', 'date_returned', 'ref_number', DB::raw("'faculty' as user_type"))
            ->get();

        // Get student fines
        $studentFines = StudentFine::with(['student', 'copy.book'])
            ->select('student_id as user_id', 'created_at as transaction_date', 'status', 'date_paid', DB::raw("'student' as user_type"))
            ->get();

        // Get faculty fines
        $facultyFines = FacultyFine::with(['faculty', 'copy.book'])
            ->select('faculty_id as user_id', 'created_at as transaction_date', 'status', 'date_paid', DB::raw("'faculty' as user_type"))
            ->get();

        // Merge and sort by date
        $allTransactions = collect()
            ->merge($studentBorrows->map(function ($transaction) {
                $transactionType = $transaction->date_returned ? 'Return' : 'Borrow';
                $statusColor = $transaction->date_returned ? 'green' : 'blue';
                $date = $transaction->date_returned ?: $transaction->transaction_date;

                return [
                    'user_name' => $transaction->student->first_name . ' ' . $transaction->student->last_name,
                    'user_type' => 'Student',
                    'transaction_date' => $date,
                    'transaction_type' => $transactionType,
                    'status_color' => $statusColor,
                ];
            }))
            ->merge($facultyBorrows->map(function ($transaction) {
                $transactionType = $transaction->date_returned ? 'Return' : 'Borrow';
                $statusColor = $transaction->date_returned ? 'green' : 'blue';
                $date = $transaction->date_returned ?: $transaction->transaction_date;

                return [
                    'user_name' => $transaction->faculty->first_name . ' ' . $transaction->faculty->last_name,
                    'user_type' => 'Faculty',
                    'transaction_date' => $date,
                    'transaction_type' => $transactionType,
                    'status_color' => $statusColor,
                ];
            }))
            ->merge($studentFines->map(function ($transaction) {
                return [
                    'user_name' => $transaction->student->first_name . ' ' . $transaction->student->last_name,
                    'user_type' => 'Student',
                    'transaction_date' => $transaction->date_paid ?: $transaction->transaction_date,
                    'transaction_type' => 'Pay Fine',
                    'status_color' => $transaction->status == 1 ? 'green' : 'red',
                ];
            }))
            ->merge($facultyFines->map(function ($transaction) {
                return [
                    'user_name' => $transaction->faculty->first_name . ' ' . $transaction->faculty->last_name,
                    'user_type' => 'Faculty',
                    'transaction_date' => $transaction->date_paid ?: $transaction->transaction_date,
                    'transaction_type' => 'Pay Fine',
                    'status_color' => $transaction->status == 1 ? 'green' : 'red',
                ];
            }))
            ->sortByDesc('transaction_date')
            ->take(10);

        return $allTransactions;
    }
}