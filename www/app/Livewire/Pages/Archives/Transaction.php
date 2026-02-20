<?php

namespace App\Livewire\Pages\Archives;

use App\Models\StudentFineArchive;
use App\Models\FacultyFineArchive;
use App\Models\TransactionArchive;
use Livewire\Component;
use Livewire\WithPagination;

use Livewire\Attributes\Lazy;

#[Lazy] class Transaction extends Component
{
    use WithPagination;

    public $transactionType = 'all'; // all, student_fines, faculty_fines, borrows, library

    public function updatedTransactionType()
    {
        $this->resetPage();
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
        $transactions = collect();

        switch ($this->transactionType) {
            case 'student_fines':
                $transactions = StudentFineArchive::orderBy('created_at', 'desc')
                    ->paginate(10);
                break;

            case 'faculty_fines':
                $transactions = FacultyFineArchive::orderBy('created_at', 'desc')
                    ->paginate(10);
                break;

            case 'library':
                $transactions = TransactionArchive::whereNotNull('library_transaction_id')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
                break;

            case 'borrows':
                $transactions = TransactionArchive::where(function ($query) {
                    $query->whereNotNull('student_borrow_transaction_id')
                        ->orWhereNotNull('faculty_borrow_transaction_id');
                })
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
                break;

            default: // all
                // Get all transaction types and merge them
                $studentFines = StudentFineArchive::orderBy('created_at', 'desc')->limit(10)->get();
                $facultyFines = FacultyFineArchive::orderBy('created_at', 'desc')->limit(10)->get();
                $otherTransactions = TransactionArchive::orderBy('created_at', 'desc')->limit(10)->get();

                $transactions = $studentFines->concat($facultyFines)->concat($otherTransactions)
                    ->sortByDesc('created_at')
                    ->take(10);
                break;
        }

        return view('livewire.pages.archives.transaction', [
            'transactions' => $transactions,
        ]);
    }
}
