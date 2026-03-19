<?php

namespace App\Livewire\Pages\Archives;

use App\Models\StudentFineArchive;
use App\Models\FacultyFineArchive;
use App\Models\LibraryTransaction;
use App\Models\TransactionArchive;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;

#[Lazy] class Transaction extends Component
{
    use WithPagination;

    public $transactionType = 'all'; // all, student_fines, faculty_fines, borrows, library

    public array $selectedIds  = [];
    public bool  $selectAll    = false;
    public ?int  $deletingId   = null;
    public ?string $deletingModel = null; // 'student_fine' | 'faculty_fine' | 'transaction'

    // ── Filters ───────────────────────────────────────────────────────────────

    public function updatedTransactionType(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll   = false;
    }

    // ── Single Delete ─────────────────────────────────────────────────────────

    public function deleteConfirmed(): void
    {
        if (!$this->deletingId || !$this->deletingModel) return;

        $record = $this->resolveModel($this->deletingModel)::findOrFail($this->deletingId);

        $label = match ($this->deletingModel) {
            'student_fine' => "Student Fine (ID: {$record->student_id}, Copy: {$record->copy_id})",
            'faculty_fine' => "Faculty Fine (ID: {$record->faculty_id}, Copy: {$record->copy_id})",
            default        => "Transaction \"{$record->name}\"",
        };

        LibraryTransaction::create([
            'transaction_name' => "Archive Permanently Deleted - {$label} - By Admin",
            'ref_number'       => $this->generateUniqueRefNumber(),
        ]);

        $record->delete();

        $this->deletingId    = null;
        $this->deletingModel = null;
        $this->selectedIds   = [];

        session()->flash('message', 'Transaction permanently deleted.');
    }

    // ── Bulk Delete ───────────────────────────────────────────────────────────

    public function deleteSelectedConfirmed(): void
    {
        if (empty($this->selectedIds)) return;

        $modelClass = match ($this->transactionType) {
            'student_fines' => StudentFineArchive::class,
            'faculty_fines' => FacultyFineArchive::class,
            default         => TransactionArchive::class,
        };

        $count = count($this->selectedIds);
        $type  = ucfirst(str_replace('_', ' ', $this->transactionType));

        LibraryTransaction::create([
            'transaction_name' => "Bulk Archive Delete ({$count} {$type} record(s)) - By Admin",
            'ref_number'       => $this->generateUniqueRefNumber(),
        ]);

        $modelClass::whereIn('id', $this->selectedIds)->delete();

        $this->selectedIds = [];
        $this->selectAll   = false;

        session()->flash('message', "{$count} transaction(s) permanently deleted.");
    }

    // ── Select-all helper ─────────────────────────────────────────────────────

    public function updatedSelectAll(bool $value): void
    {
        if (!$value) {
            $this->selectedIds = [];
            return;
        }

        $modelClass = match ($this->transactionType) {
            'student_fines' => StudentFineArchive::class,
            'faculty_fines' => FacultyFineArchive::class,
            default         => TransactionArchive::class,
        };

        $this->selectedIds = $modelClass::pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    // ── Internal ──────────────────────────────────────────────────────────────

    private function resolveModel(string $type): string
    {
        return match ($type) {
            'student_fine' => StudentFineArchive::class,
            'faculty_fine' => FacultyFineArchive::class,
            default        => TransactionArchive::class,
        };
    }

    private function generateUniqueRefNumber(): string
    {
        do {
            $ref = 'REF-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        } while (LibraryTransaction::where('ref_number', $ref)->exists());

        return $ref;
    }

    // ── Placeholder / Render ──────────────────────────────────────────────────

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

            default:
                $studentFines      = StudentFineArchive::orderBy('created_at', 'desc')->limit(10)->get();
                $facultyFines      = FacultyFineArchive::orderBy('created_at', 'desc')->limit(10)->get();
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
