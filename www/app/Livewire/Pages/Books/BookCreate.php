<?php

namespace App\Livewire\Pages\Books;

use Livewire\Component;
use App\Models\Book;
use App\Models\Department;
use App\Models\Course;
use App\Models\LibraryTransaction;
use App\Models\Copy;
use Illuminate\Support\Str;
use Flux\Flux;

class BookCreate extends Component
{
    public $title;
    public $author;
    public $publisher;
    public $isbn;
    public $publication_date;
    public $department_id = null;
    public $course_id = null;
    public $category;
    public $copies = 1;

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

    public function mount()
    {
        $this->departments = Department::all();
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

    public function saveBook()
    {
        $this->validate();

        try {
            $book = Book::create([
                'title' => $this->title,
                'author' => $this->author,
                'publisher' => $this->publisher,
                'isbn' => $this->isbn,
                'publication_date' => $this->publication_date,
                'department_id' => $this->department_id,
                'category' => $this->category,
                'copies' => $this->copies,
            ]);

            $refNumber = $this->generateUniqueRefNumber();

            LibraryTransaction::create([
                'transaction_name' => 'Add Book',
                'ref_number' => $refNumber,
            ]);

            $this->generateCopies($book);

            $this->reset(['title', 'author', 'publisher', 'isbn', 'publication_date', 'department_id', 'course_id', 'category', 'copies']);
            $this->filteredCourses = [];

            $this->dispatch('bookCreated');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to add book: ' . $e->getMessage());
        }
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

            $refNumber = 'BCU-ADDBK-' . $randomString;
        } while (LibraryTransaction::where('ref_number', $refNumber)->exists());

        return $refNumber;
    }

    private function generateCopies(Book $book)
    {
        $titleWords = explode(' ', $book->title);
        $initials = '';
        foreach ($titleWords as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }

        $deptCode = $book->department_id;

        for ($i = 1; $i <= $book->copies; $i++) {
            $copyNumber = str_pad($i, 3, '0', STR_PAD_LEFT);
            $copyId = $initials . $deptCode . $copyNumber;

            Copy::create([
                'copy_id' => $copyId,
                'book_id' => $book->id,
                'course_id' => $this->course_id,
                'status' => 'Available',
            ]);
        }
    }

    public function closeModal()
    {
        $this->dispatch('closeCreate');
    }

    public function render()
    {
        return view('livewire.pages.books.book-create');
    }
}
