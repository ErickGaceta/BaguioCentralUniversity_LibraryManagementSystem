<?php

namespace App\Livewire\Pages\Copies;

use Livewire\Component;
use App\Models\Copy;
use App\Models\Student;
use App\Models\Faculty;
use App\Models\LibraryTransaction;
use Illuminate\Support\Str;

class CopyShow extends Component
{
    public $copyId;
    public $copy;
    public $borrowerType = 'student';
    public $borrowerId = '';
    public $dueDate = '';
    public $returnCondition = '';
    public $activeTab = 'info';
    public $borrowerSearch = '';
    public $borrowerResults = [];

    // Clear search results when borrower type changes
    public function updatedBorrowerType()
    {
        $this->borrowerSearch = '';
        $this->borrowerResults = [];
        $this->borrowerId = '';
    }

    public function updatedBorrowerSearch($value)
    {
        // Clear results if search is too short
        if (strlen(trim($value)) < 2) {
            $this->borrowerResults = [];
            return;
        }

        $searchTerm = trim($value);

        if ($this->borrowerType === 'student') {
            $students = Student::query()
                ->where(function ($q) use ($searchTerm) {
                    $q->where('first_name', 'like', "%{$searchTerm}%")
                        ->orWhere('middle_name', 'like', "%{$searchTerm}%")
                        ->orWhere('last_name', 'like', "%{$searchTerm}%")
                        ->orWhere('student_id', 'like', "%{$searchTerm}%");
                })
                ->limit(10)
                ->get();

            $this->borrowerResults = $students->map(function ($student) {
                return [
                    'id' => $student->student_id,
                    'full_name' => $student->full_name,
                ];
            })->toArray();
        } else {
            $faculties = Faculty::query()
                ->where(function ($q) use ($searchTerm) {
                    $q->where('first_name', 'like', "%{$searchTerm}%")
                        ->orWhere('middle_name', 'like', "%{$searchTerm}%")
                        ->orWhere('last_name', 'like', "%{$searchTerm}%")
                        ->orWhere('faculty_id', 'like', "%{$searchTerm}%");
                })
                ->limit(10)
                ->get();

            $this->borrowerResults = $faculties->map(function ($faculty) {
                return [
                    'id' => $faculty->faculty_id,
                    'full_name' => $faculty->full_name,
                ];
            })->toArray();
        }
    }

    public function selectBorrower($id)
    {
        $this->borrowerId = $id;

        // Get the selected person's name for display
        if ($this->borrowerType === 'student') {
            $student = Student::where('student_id', $id)->first();
            $this->borrowerSearch = $student ? $student->full_name . ' — ' . $id : $id;
        } else {
            $faculty = Faculty::where('faculty_id', $id)->first();
            $this->borrowerSearch = $faculty ? $faculty->full_name . ' — ' . $id : $id;
        }

        $this->borrowerResults = [];
    }

    public function mount($copyId)
    {
        $this->copyId = $copyId;
        $this->copy = Copy::with([
            'book',
            'course',
            'studentBorrows' => fn($q) => $q->with('student')->latest('date_borrowed'),
            'facultyBorrows' => fn($q) => $q->with('faculty')->latest('date_borrowed'),
        ])->findOrFail($copyId);
    }

    // ── Borrow ──────────────────────────────────────────────────────────────────
    public function borrow(): void
    {
        $this->validate([
            'borrowerType' => 'required|in:student,faculty',
            'borrowerId' => 'required|string',
            'dueDate' => 'required|date|after:today',
        ], [
            'dueDate.after' => 'Due date must be in the future.',
            'borrowerId.required' => 'Please select a ' . $this->borrowerType . ' from the search results.',
        ], [
            'borrowerType' => 'borrower type',
            'borrowerId' => 'borrower',
            'dueDate' => 'due date',
        ]);

        try {
            // Generate ref number based on borrower type
            $refNumber = $this->generateUniqueRefNumber('BORROW', $this->borrowerType);

            // Set transaction name based on borrower type
            $transactionName = $this->borrowerType === 'student' ? 'Student Borrow' : 'Faculty Borrow';

            if ($this->borrowerType === 'student') {
                $student = Student::where('student_id', $this->borrowerId)->firstOrFail();
                $this->copy->borrowByStudent($student->student_id, $refNumber, $this->dueDate);
            } else {
                $faculty = Faculty::where('faculty_id', $this->borrowerId)->firstOrFail();
                $this->copy->borrowByFaculty($faculty->faculty_id, $refNumber, $this->dueDate);
            }

            $this->reset(['borrowerId', 'dueDate', 'borrowerSearch', 'borrowerResults']);
            $this->activeTab = 'info';
            $this->dispatch('copyUpdated');

            session()->flash('message', 'Copy has been marked as borrowed.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            $this->addError('borrowerId', 'No ' . $this->borrowerType . ' found with that ID.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    // ── Return ──────────────────────────────────────────────────────────────────
    public function return(): void
    {
        $this->validate([
            'returnCondition' => 'required|in:Good,Minor Damage,Major Damage,Total Damage, Lost Book',
        ], [], [
            'returnCondition' => 'condition',
        ]);

        try {
            // Generate return reference number FIRST
            $hasStudentBorrow = $this->copy->studentBorrows()
                ->whereNull('date_returned')
                ->exists();

            $borrowerType = $hasStudentBorrow ? 'student' : 'faculty';
            $returnRefNumber = $this->generateUniqueRefNumber('RETURN', $borrowerType);

            $this->copy->returnCopy(
                $this->returnCondition,
                $returnRefNumber,
                type: $borrowerType
            );

            $this->reset(['returnCondition']);
            $this->activeTab = 'info';
            $this->dispatch('copyUpdated');

            session()->flash('message', 'Copy has been returned and marked as available.');
        } catch (\LogicException $e) {
            session()->flash('error', $e->getMessage());
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while returning the copy: ' . $e->getMessage());
        }
    }

    // ── Shared ──────────────────────────────────────────────────────────────────
    public function closeModal(): void
    {
        $this->dispatch('closeEdit');
    }

    private function generateUniqueRefNumber(string $action, string $type = null): string
    {
        $prefix = match ($action) {
            'BORROW' => match ($type) {
                'student' => 'STDNT-BRRW-',
                'faculty' => 'FCLT-BRRW-',
                default => 'BCU-BRRW-',
            },
            'RETURN' => match ($type) {
                'student' => 'STDNT-RTRN-',
                'faculty' => 'FCLT-RTRN-',
                default => 'BCU-RTRN-',
            },
        };

        do {
            $random = strtoupper(preg_replace('/[^A-Z0-9]/', '', Str::random(20)));
            while (strlen($random) < 15) {
                $random .= strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 1));
            }
            $refNumber = $prefix . substr($random, 0, 15);
        } while (LibraryTransaction::where('ref_number', $refNumber)->exists());

        return $refNumber;
    }

    public function render()
    {
        // Re-fetch so the view always reflects latest DB state after borrow/return
        $this->copy = Copy::with([
            'book',
            'course',
            'accession',
            'studentBorrows' => fn($q) => $q->with('student')->latest('date_borrowed'),
            'facultyBorrows' => fn($q) => $q->with('faculty')->latest('date_borrowed'),
        ])->findOrFail($this->copyId);

        return view('livewire.pages.copies.copy-show', [
            'copy' => $this->copy,
            'conditions' => ['Good', 'Minor Damage', 'Major Damage', 'Total Damage'],
        ]);
    }
}
