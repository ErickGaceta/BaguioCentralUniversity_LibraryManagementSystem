<?php

namespace App\Livewire\Pages\Books;

use Livewire\Component;
use App\Models\Book;
use App\Models\Department;
use App\Models\Course;
use App\Models\LibraryTransaction;
use App\Models\Copy;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Flux\Flux;

class BookEdit extends Component
{
    public $bookId;
    public $book;

    public $title;
    public $author;
    public $publisher;
    public $isbn;
    public $publication_date;
    public $department_id = null;
    public $course_id = null;
    public $category;
    public $copies = 1;

    // Track original values
    public $originalCopies = 0;
    public $originalData = [];

    public $departments;
    public $filteredCourses = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'author' => 'required|string|max:255',
        'publisher' => 'nullable|string|max:255',
        'isbn' => 'nullable|string|max:50',
        'publication_date' => 'nullable|date',
        'department_id' => 'required|exists:departments,department_code',
        'course_id' => 'required|exists:courses,course_code',
        'category' => 'nullable|string|max:100',
        'copies' => 'required|integer|min:1|max:100',
    ];

    public function mount($bookId)
    {
        $this->bookId = $bookId;
        $this->book = Book::findOrFail($bookId);

        // Load departments
        $this->departments = Department::all();

        // Populate form with existing book data
        $this->title = $this->book->title;
        $this->author = $this->book->author;
        $this->publisher = $this->book->publisher;
        $this->isbn = $this->book->isbn;
        $this->publication_date = $this->book->publication_date;
        $this->department_id = $this->book->department_id;
        $this->category = $this->book->category;
        $this->copies = $this->book->copies;
        $this->originalCopies = $this->book->copies;

        // Load courses for the book's department
        if ($this->department_id) {
            $this->filteredCourses = Course::where('department_id', $this->department_id)->get();

            // Get course_id from the first copy (assuming all copies have the same course)
            $firstCopy = Copy::where('book_id', $this->book->id)->first();
            if ($firstCopy) {
                $this->course_id = $firstCopy->course_id;
            }
        }

        // Store original data for change detection
        $this->originalData = [
            'title' => $this->title,
            'author' => $this->author,
            'publisher' => $this->publisher,
            'isbn' => $this->isbn,
            'publication_date' => $this->publication_date,
            'department_id' => $this->department_id,
            'course_id' => $this->course_id,
            'category' => $this->category,
            'copies' => $this->copies,
        ];
    }

    // This method is automatically called when department_id changes
    public function updatedDepartmentId($value)
    {
        // Reset course selection when department changes
        $this->course_id = null;

        // Filter courses by selected department
        if ($value) {
            $this->filteredCourses = Course::where('department_id', $value)->get();
        } else {
            $this->filteredCourses = [];
        }
    }

    public function updateBook()
    {
        $this->validate();

        // Check if anything has changed
        if (!$this->hasChanges()) {
            session()->flash('info', 'No changes detected.');
            $this->dispatch('bookUpdated');
            return;
        }

        DB::beginTransaction();

        try {
            // Update the book
            $this->book->update([
                'title' => $this->title,
                'author' => $this->author,
                'publisher' => $this->publisher,
                'isbn' => $this->isbn,
                'publication_date' => $this->publication_date,
                'department_id' => $this->department_id,
                'category' => $this->category,
                'copies' => $this->copies,
            ]);

            // Handle copy count changes
            $this->handleCopyCountChange();

            // Update course_id for all copies if it changed
            if ($this->course_id && $this->course_id !== $this->originalData['course_id']) {
                Copy::where('book_id', $this->book->id)
                    ->update(['course_id' => $this->course_id]);
            }

            // Generate unique reference number
            $refNumber = $this->generateUniqueRefNumber();

            // Create library transaction
            LibraryTransaction::create([
                'transaction_name' => 'Edit Book',
                'ref_number' => $refNumber,
            ]);

            DB::commit();

            session()->flash('success', 'Book updated successfully!');
            $this->dispatch('bookUpdated');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to update book: ' . $e->getMessage());
        }
    }

    private function hasChanges()
    {
        $currentData = [
            'title' => $this->title,
            'author' => $this->author,
            'publisher' => $this->publisher,
            'isbn' => $this->isbn,
            'publication_date' => $this->publication_date,
            'department_id' => $this->department_id,
            'course_id' => $this->course_id,
            'category' => $this->category,
            'copies' => $this->copies,
        ];

        // Compare current data with original data
        foreach ($currentData as $key => $value) {
            if ($value != $this->originalData[$key]) {
                return true;
            }
        }

        return false;
    }

    private function handleCopyCountChange()
    {
        $currentCopyCount = Copy::where('book_id', $this->book->id)->count();
        $newCopyCount = $this->copies;

        if ($newCopyCount > $currentCopyCount) {
            // Add new copies
            $this->addCopies($newCopyCount - $currentCopyCount);
        } elseif ($newCopyCount < $currentCopyCount) {
            // Remove excess copies (last ones)
            $this->removeCopies($currentCopyCount - $newCopyCount);
        }

        // If count is the same, do nothing
    }

    private function addCopies($count)
    {
        // Get book initials (first letter of each word in title)
        $titleWords = explode(' ', $this->book->title);
        $initials = '';
        foreach ($titleWords as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }

        // Get department code
        $deptCode = $this->book->department_id;

        // Get the current maximum copy number for this book
        $existingCopies = Copy::where('book_id', $this->book->id)
            ->orderBy('copy_id', 'desc')
            ->get();

        // Extract the last number from the most recent copy
        $lastCopyNumber = 0;
        if ($existingCopies->count() > 0) {
            // Extract number from copy_id (e.g., "TITLEDEPT001" -> 1)
            $lastCopyId = $existingCopies->first()->copy_id;
            preg_match('/(\d+)$/', $lastCopyId, $matches);
            if (isset($matches[1])) {
                $lastCopyNumber = intval($matches[1]);
            }
        }

        // Generate new copies starting from the next number
        for ($i = 1; $i <= $count; $i++) {
            $copyNumber = str_pad($lastCopyNumber + $i, 3, '0', STR_PAD_LEFT);
            $copyId = $initials . $deptCode . $copyNumber;

            Copy::create([
                'copy_id' => $copyId,
                'book_id' => $this->book->id,
                'course_id' => $this->course_id,
                'status' => 'Available',
                'condition' => 'Good',
            ]);
        }
    }

    private function removeCopies($count)
    {
        // Get the last N copies ordered by copy_id descending
        $copiesToRemove = Copy::where('book_id', $this->book->id)
            ->orderBy('copy_id', 'desc')
            ->limit($count)
            ->get();

        // Check if any of these copies are currently borrowed
        foreach ($copiesToRemove as $copy) {
            if ($copy->status !== 'Available') {
                throw new \Exception("Cannot remove copies that are currently borrowed or unavailable. Copy ID: {$copy->copy_id}");
            }
        }

        // Delete the copies
        Copy::where('book_id', $this->book->id)
            ->orderBy('copy_id', 'desc')
            ->limit($count)
            ->delete();
    }

    private function generateUniqueRefNumber()
    {
        do {
            $randomString = strtoupper(Str::random(15));
            $randomString = preg_replace('/[^A-Z0-9]/', '', $randomString);
            while (strlen($randomString) < 15) {
                $randomString .= strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 1));
            }
            $randomString = substr($randomString, 0, 15);

            $refNumber = 'BCU-EDITBK-' . $randomString;
        } while (LibraryTransaction::where('ref_number', $refNumber)->exists());

        return $refNumber;
    }

    public function closeModal()
    {
        $this->dispatch('closeEdit');
    }

    public function render()
    {
        return view('livewire.pages.books.book-edit');
    }
}
