<?php

namespace App\Livewire\Pages\Transactions;

use App\Models\FacultyBorrow;
use App\Models\StudentBorrow;
use App\Models\TransactionArchive;
use App\Services\ArchiveTransactionService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

use Livewire\Attributes\Lazy;

#[Lazy] class Issuance extends Component
{
    use WithPagination;

    public $perPage = 20;

    public function archiveTransaction($type, $id)
    {
        if ($type === 'student') {
            $record = StudentBorrow::find($id);
            if ($record) {
                TransactionArchive::create([
                    'student_borrow_transaction_id' => (string) $record->id,
                    'name' => 'Student Borrow - Ref: ' . $record->ref_number,
                ]);
                $record->delete();
                session()->flash('message', 'Student issuance transaction has been archived.');
            }
        } elseif ($type === 'faculty') {
            $record = FacultyBorrow::find($id);
            if ($record) {
                TransactionArchive::create([
                    'faculty_borrow_transaction_id' => (string) $record->id,
                    'name' => 'Faculty Borrow - Ref: ' . $record->ref_number,
                ]);
                $record->delete();
                session()->flash('message', 'Faculty issuance transaction has been archived.');
            }
        }
    }
    public ?int $archivingTransactionId = null;
    public ?string $archivingTransactionType = null;

    public function prepareArchiveTransaction(string $type, int $id): void
    {
        $this->archivingTransactionType = $type;
        $this->archivingTransactionId = $id;
        $this->dispatch('open-archive-transaction-modal');
    }

    public function archiveTransactionConfirmed(): void
    {
        if (!$this->archivingTransactionId) return;

        $this->archiveTransaction($this->archivingTransactionType, $this->archivingTransactionId);
        $this->archivingTransactionId = null;
        $this->archivingTransactionType = null;
    }

    public function placeholder()
    {
        return <<<'HTML'
            <div class="w-full h-full flex justify-center items-center align-center">
                <span class="loader"></span>
            </div>
        HTML;
    }

    public function render()
    {
        // Student BORROW transactions
        $studentBorrowQuery = StudentBorrow::select(
            'student_borrows.id',
            'student_borrows.student_id as user_id',
            'student_borrows.ref_number',
            'student_borrows.date_borrowed as transaction_date',
            DB::raw("'student' as type"),
            DB::raw("'Student Borrow' as transaction_name"),
            DB::raw("students.first_name || ' ' || COALESCE(students.middle_name || ' ', '') || students.last_name as name"),
            'students.student_id as display_id'
        )
            ->join('students', 'students.student_id', '=', 'student_borrows.student_id');

        // Student RETURN transactions (only those that have been returned)
        $studentReturnQuery = StudentBorrow::select(
            'student_borrows.id',
            'student_borrows.student_id as user_id',
            'student_borrows.return_ref_number as ref_number',
            'student_borrows.date_returned as transaction_date',
            DB::raw("'student' as type"),
            DB::raw("'Student Return' as transaction_name"),
            DB::raw("students.first_name || ' ' || COALESCE(students.middle_name || ' ', '') || students.last_name as name"),
            'students.student_id as display_id'
        )
            ->join('students', 'students.student_id', '=', 'student_borrows.student_id')
            ->whereNotNull('student_borrows.date_returned')
            ->whereNotNull('student_borrows.return_ref_number');

        // Faculty BORROW transactions
        $facultyBorrowQuery = FacultyBorrow::select(
            'faculty_borrows.id',
            'faculty_borrows.faculty_id as user_id',
            'faculty_borrows.ref_number',
            'faculty_borrows.date_borrowed as transaction_date',
            DB::raw("'faculty' as type"),
            DB::raw("'Faculty Borrow' as transaction_name"),
            DB::raw("faculties.first_name || ' ' || COALESCE(faculties.middle_name || ' ', '') || faculties.last_name as name"),
            'faculties.faculty_id as display_id'
        )
            ->join('faculties', 'faculties.faculty_id', '=', 'faculty_borrows.faculty_id');

        // Faculty RETURN transactions (only those that have been returned)
        $facultyReturnQuery = FacultyBorrow::select(
            'faculty_borrows.id',
            'faculty_borrows.faculty_id as user_id',
            'faculty_borrows.return_ref_number as ref_number',
            'faculty_borrows.date_returned as transaction_date',
            DB::raw("'faculty' as type"),
            DB::raw("'Faculty Return' as transaction_name"),
            DB::raw("faculties.first_name || ' ' || COALESCE(faculties.middle_name || ' ', '') || faculties.last_name as name"),
            'faculties.faculty_id as display_id'
        )
            ->join('faculties', 'faculties.faculty_id', '=', 'faculty_borrows.faculty_id')
            ->whereNotNull('faculty_borrows.date_returned')
            ->whereNotNull('faculty_borrows.return_ref_number');

        // Union all transactions
        $mergedQuery = $studentBorrowQuery
            ->unionAll($studentReturnQuery)
            ->unionAll($facultyBorrowQuery)
            ->unionAll($facultyReturnQuery);

        // Wrap in paginator
        $page = request()->get('page', 1);
        $perPage = $this->perPage;

        $items = DB::table(DB::raw("({$mergedQuery->toSql()}) as merged"))
            ->mergeBindings($mergedQuery->getQuery())
            ->orderByDesc('transaction_date')
            ->forPage($page, $perPage)
            ->get();

        // Calculate total count
        $total = $studentBorrowQuery->count()
            + $studentReturnQuery->count()
            + $facultyBorrowQuery->count()
            + $facultyReturnQuery->count();

        $paginatedBorrows = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.pages.transactions.issuance', [
            'borrows' => $paginatedBorrows,
        ]);
    }
}
