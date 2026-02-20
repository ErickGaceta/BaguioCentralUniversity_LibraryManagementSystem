<?php

namespace App\Livewire\Pages\Books;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Book;
use App\Models\Department;
use App\Models\Course;
use App\Models\LibraryTransaction;
use App\Models\Copy;
use App\Models\CopyAccession;
use Illuminate\Support\Str;

class BookCreate extends Component
{
    public $title;
    public $author;
    public $publisher;
    public $isbn;
    public $publication_date;
    public $department_id = null;
    public $course_id     = null;
    public $category;
    public $copies = 1;

    public $departments;
    public $filteredCourses = [];

    protected $rules = [
        'title'            => 'required|string|max:255',
        'author'           => 'required|string|max:255',
        'publisher'        => 'nullable|string|max:255',
        'isbn'             => 'nullable|string|max:50',
        'publication_date' => 'nullable|string|max:50',
        'department_id'    => 'required|exists:departments,department_code',
        'course_id'        => 'required|exists:courses,course_code',
        'category'         => 'nullable|string|max:100',
        'copies'           => 'required|integer|min:1|max:100',
    ];

    public function mount(): void
    {
        $this->departments = Department::all();
    }

    public function updatedDepartmentId($value): void
    {
        $this->course_id       = null;
        $this->filteredCourses = $value
            ? Course::where('department_id', $value)->get()
            : collect();

        // Auto-select first course after reset
        $first = collect($this->filteredCourses)->first();
        if ($first) {
            $this->course_id = $first->course_code;
        }
    }

    /**
     * Validates the book form, then opens BookCatalog with all copies as "new".
     * Alpine passes the current copyCount since it owns that input.
     */
    public function openCatalogModal(int $copiesCount): void
    {
        $this->copies = $copiesCount;

        $this->validate();

        // For creation, there are no existing copies — everything is new
        $this->dispatch(
            'open-book-catalog',
            title: $this->title,
            existingCopies: [],
            newCopiesCount: (int) $this->copies,
        );
    }

    /**
     * Receives validated catalog data from BookCatalog.
     * In create mode, existingCatalogData will always be empty.
     */
    #[On('catalog-ready')]
    public function saveBook(array $existingCatalogData = [], array $newCopyData = []): void
    {
        $this->validate();

        try {
            $book = Book::create([
                'title'            => $this->title,
                'author'           => $this->author,
                'publisher'        => $this->publisher,
                'isbn'             => $this->isbn,
                'publication_date' => $this->publication_date,
                'department_id'    => $this->department_id,
                'category'         => $this->category,
                'copies'           => $this->copies,
            ]);

            LibraryTransaction::create([
                'transaction_name' => 'Add Book',
                'ref_number'       => $this->generateUniqueRefNumber(),
            ]);

            $this->generateCopies($book, $newCopyData);

            $this->reset([
                'title',
                'author',
                'publisher',
                'isbn',
                'publication_date',
                'department_id',
                'course_id',
                'category',
                'copies'
            ]);
            $this->filteredCourses = [];

            $this->dispatch('bookCreated');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to add book: ' . $e->getMessage());
        }
    }

    private function generateUniqueRefNumber(): string
    {
        do {
            $random = strtoupper(Str::random(15));
            $random = preg_replace('/[^A-Z0-9]/', '', $random);
            while (strlen($random) < 15) {
                $random .= strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 1));
            }
            $refNumber = 'BCU-ADDBK-' . substr($random, 0, 15);
        } while (LibraryTransaction::where('ref_number', $refNumber)->exists());

        return $refNumber;
    }

    private function generateCopies(Book $book, array $copyData): void
    {
        $initials = '';
        foreach (explode(' ', $book->title) as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }

        foreach ($copyData as $i => $row) {
            $copyId = $initials . $book->department_id . str_pad($i + 1, 3, '0', STR_PAD_LEFT);

            $copy = Copy::create([
                'copy_id'   => $copyId,
                'book_id'   => $book->id,
                'course_id' => $this->course_id,
                'status'    => 'Available',
                'condition' => 'Good',
            ]);

            CopyAccession::create([
                'copy_id'          => $copy->copy_id,
                'accession_number' => trim($row['accession_number']),
                'call_number'      => trim($row['call_number']),
            ]);
        }
    }

    public function closeModal(): void
    {
        $this->dispatch('closeCreate');
    }

    public function render()
    {
        return view('livewire.pages.books.book-create', [
            'departments' => $this->departments,
        ]);
    }
}
