<?php

namespace App\Livewire;

use App\Models\Book;
use App\Models\Copy;
use App\Models\Student;
use App\Models\Faculty;
use App\Models\StudentBorrow;
use App\Models\FacultyBorrow;
use App\Models\StudentFine;
use App\Models\FacultyFine;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $stats = [];
    public $recentTransactions = [];

    public function mount()
    {
        $this->loadStats();
        $this->loadRecentTransactions();
    }

    protected function loadStats(): void
    {
        $this->stats = [
            'total_books'     => Book::count(),
            'total_students'  => Student::count(),
            'total_faculties' => Faculty::count(),
            'total_fines'     => StudentFine::sum('amount') + FacultyFine::sum('amount'),
            'books_issued'    => StudentBorrow::whereNull('date_returned')->count()
                + FacultyBorrow::whereNull('date_returned')->count(),
            'overdue_books'   => StudentBorrow::where('due_date', '<', now()->toDateString())
                ->whereNull('date_returned')->count()
                + FacultyBorrow::where('due_date', '<', now()->toDateString())
                ->whereNull('date_returned')->count(),
        ];
    }

    protected function loadRecentTransactions(): void
    {
        $collections = [
            DB::table('library_transactions')
                ->select(
                    DB::raw("'Add Book' as transaction_name"),
                    'created_at as date',
                    DB::raw("'Admin' as made_by")
                )
                ->where('transaction_name', 'like', 'Add Book')
                ->get(),

            DB::table('library_transactions')
                ->select(
                    DB::raw("'Edit Book' as transaction_name"),
                    'created_at as date',
                    DB::raw("'Admin' as made_by")
                )
                ->where('transaction_name', 'like', 'Edit Book')
                ->get(),

            DB::table('library_transactions')
                ->select(
                    DB::raw("'Archive Book' as transaction_name"),
                    'created_at as date',
                    DB::raw("'Admin' as made_by")
                )
                ->where('transaction_name', 'like', 'Archive Book')
                ->get(),

            DB::table("student_borrows as sb")
                ->join('students as s', 'sb.student_id', '=', 's.student_id')
                ->select(
                    DB::raw("'Student Issuance' as transaction_name"),
                    'sb.date_borrowed as date',
                    DB::raw("s.student_id || ' - ' || s.first_name || ' ' || s.middle_name || ' ' || s.last_name as made_by")
                )
                ->get(),

            DB::table("student_borrows as sb")
                ->join('students as s', 'sb.student_id', '=', 's.student_id')
                ->select(
                    DB::raw("'Student Return' as transaction_name"),
                    'sb.date_returned as date',
                    DB::raw("s.student_id || ' - ' || s.first_name || ' ' || s.middle_name || ' ' || s.last_name as made_by")
                )
                ->whereNotNull('sb.date_returned')
                ->get(),

            DB::table("faculty_borrows as fb")
                ->join('faculties as f', 'fb.faculty_id', '=', 'f.faculty_id')
                ->select(
                    DB::raw("'Faculty Issuance' as transaction_name"),
                    'fb.date_borrowed as date',
                    DB::raw("f.faculty_id || ' - ' || f.first_name || ' ' || f.middle_name || ' ' || f.last_name as made_by")
                )
                ->get(),

            DB::table("faculty_borrows as fb")
                ->join('faculties as f', 'fb.faculty_id', '=', 'f.faculty_id')
                ->select(
                    DB::raw("'Faculty Return' as transaction_name"),
                    'fb.date_returned as date',
                    DB::raw("f.faculty_id || ' - ' || f.first_name || ' ' || f.middle_name || ' ' || f.last_name as made_by")
                )
                ->whereNotNull('fb.date_returned')
                ->get(),

            DB::table('library_transactions')
                ->select(
                    DB::raw("'Add Student' as transaction_name"),
                    'created_at as date',
                    DB::raw("'Admin' as made_by")
                )
                ->where('transaction_name', 'Add Student')
                ->get(),

            DB::table('library_transactions')
                ->select(
                    DB::raw("'Edit Student' as transaction_name"),
                    'created_at as date',
                    DB::raw("'Admin' as made_by")
                )
                ->where('transaction_name', 'Edit Student')
                ->get(),

            DB::table('library_transactions')
                ->select(
                    DB::raw("'Archive Student' as transaction_name"),
                    'created_at as date',
                    DB::raw("'Admin' as made_by")
                )
                ->where('transaction_name', 'Archive Student')
                ->get(),

            // ── Faculty ──────────────────────────────────────────────────────────────
            DB::table('library_transactions')
                ->select(
                    DB::raw("'Add Faculty' as transaction_name"),
                    'created_at as date',
                    DB::raw("'Admin' as made_by")
                )
                ->where('transaction_name', 'Add Faculty')
                ->get(),

            DB::table('library_transactions')
                ->select(
                    DB::raw("'Edit Faculty' as transaction_name"),
                    'created_at as date',
                    DB::raw("'Admin' as made_by")
                )
                ->where('transaction_name', 'Edit Faculty')
                ->get(),

            DB::table('library_transactions')
                ->select(
                    DB::raw("'Archive Faculty' as transaction_name"),
                    'created_at as date',
                    DB::raw("'Admin' as made_by")
                )
                ->where('transaction_name', 'Archive Faculty')
                ->get(),
        ];

        // Merge all collections
        $this->recentTransactions = collect($collections)
            ->flatten(1)
            ->sortByDesc(fn($tx) => Carbon::parse($tx->date)->timestamp)
            ->take(10)
            ->values();
    }




    public function render()
    {
        return view('livewire.dashboard');
    }
}
