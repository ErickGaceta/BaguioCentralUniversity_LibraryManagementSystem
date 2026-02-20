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
use Illuminate\Support\Facades\DB;

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
    public $course_id     = null;
    public $category;
    public $copies = 1;

    public $originalCopies = 0;
    public $originalData   = [];

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

    public function mount($bookId): void
    {
        $this->bookId      = $bookId;
        $this->book        = Book::findOrFail($bookId);
        $this->departments = Department::all();

        $this->title            = $this->book->title;
        $this->author           = $this->book->author;
        $this->publisher        = $this->book->publisher;
        $this->isbn             = $this->book->isbn;
        $this->publication_date = $this->book->publication_date;
        $this->department_id    = $this->book->department_id;
        $this->category         = $this->book->category;
        $this->copies           = $this->book->copies;
        $this->originalCopies   = $this->book->copies;

        if ($this->department_id) {
            $this->filteredCourses = Course::where('department_id', $this->department_id)->get();

            $firstCopy = Copy::where('book_id', $this->book->id)->first();
            if ($firstCopy) {
                $this->course_id = $firstCopy->course_id;
            }
        }

        $this->originalData = [
            'title'            => $this->title,
            'author'           => $this->author,
            'publisher'        => $this->publisher,
            'isbn'             => $this->isbn,
            'publication_date' => $this->publication_date,
            'department_id'    => $this->department_id,
            'course_id'        => $this->course_id,
            'category'         => $this->category,
            'copies'           => $this->copies,
        ];
    }

    public function updatedDepartmentId($value): void
    {
        $this->course_id       = null;
        $this->filteredCourses = $value
            ? Course::where('department_id', $value)->get()
            : [];
    }

    /**
     * Validates form, then decides whether to open BookCatalog or save directly.
     *
     * The catalog modal is needed when:
     *   - Some existing copies have no CopyAccession record, OR
     *   - New copies are being added
     *
     * If neither applies, we save directly.
     */
    public function openCatalogModal(int $copiesCount): void
    {
        $this->copies = $copiesCount;

        $this->validate();

        if (!$this->hasChanges()) {
            session()->flash('info', 'No changes detected.');
            $this->dispatch('bookUpdated');
            return;
        }

        // Existing copies that have no accession record yet
        $uncataloged = Copy::where('book_id', $this->book->id)
            ->whereDoesntHave('accession')
            ->get()
            ->map(fn($c) => [
                'copy_id' => $c->copy_id,
                'label'   => $c->copy_id,
            ])
            ->values()
            ->toArray();

        $newCopiesCount = max(0, (int) $this->copies - Copy::where('book_id', $this->book->id)->count());

        // If nothing needs cataloging, save straight away
        if (empty($uncataloged) && $newCopiesCount === 0) {
            $this->performUpdate([], []);
            return;
        }

        $this->dispatch('open-book-catalog',
            title:          $this->title,
            existingCopies: $uncataloged,
            newCopiesCount: $newCopiesCount,
        );
    }

    /**
     * Receives validated catalog data from BookCatalog.
     *
     * $existingCatalogData : [{copy_id, label, accession_number, call_number}]
     * $newCopyData         : [{accession_number, call_number}]
     */
    #[On('catalog-ready')]
    public function saveWithCatalog(array $existingCatalogData = [], array $newCopyData = []): void
    {
        $this->performUpdate($existingCatalogData, $newCopyData);
    }

    private function performUpdate(array $existingCatalogData, array $newCopyData): void
    {
        DB::beginTransaction();

        try {
            $this->book->update([
                'title'            => $this->title,
                'author'           => $this->author,
                'publisher'        => $this->publisher,
                'isbn'             => $this->isbn,
                'publication_date' => $this->publication_date,
                'department_id'    => $this->department_id,
                'category'         => $this->category,
                'copies'           => $this->copies,
            ]);

            // Create accession records for previously uncataloged existing copies
            foreach ($existingCatalogData as $row) {
                CopyAccession::create([
                    'copy_id'          => $row['copy_id'],
                    'accession_number' => trim($row['accession_number']),
                    'call_number'      => trim($row['call_number']),
                ]);
            }

            // Handle copy count changes (add or remove)
            $this->handleCopyCountChange($newCopyData);

            // Update course_id across all copies if it changed
            if ($this->course_id && $this->course_id !== $this->originalData['course_id']) {
                Copy::where('book_id', $this->book->id)
                    ->update(['course_id' => $this->course_id]);
            }

            LibraryTransaction::create([
                'transaction_name' => 'Edit Book',
                'ref_number'       => $this->generateUniqueRefNumber(),
            ]);

            DB::commit();

            session()->flash('success', 'Book updated successfully!');
            $this->dispatch('bookUpdated');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('general', 'Failed to update book: ' . $e->getMessage());
        }
    }

    private function hasChanges(): bool
    {
        $current = [
            'title'            => $this->title,
            'author'           => $this->author,
            'publisher'        => $this->publisher,
            'isbn'             => $this->isbn,
            'publication_date' => $this->publication_date,
            'department_id'    => $this->department_id,
            'course_id'        => $this->course_id,
            'category'         => $this->category,
            'copies'           => $this->copies,
        ];

        foreach ($current as $key => $value) {
            if ($value != $this->originalData[$key]) {
                return true;
            }
        }

        return false;
    }

    private function handleCopyCountChange(array $newCopyData): void
    {
        $currentCount = Copy::where('book_id', $this->book->id)->count();
        $newCount     = (int) $this->copies;

        if ($newCount > $currentCount) {
            $this->addCopies($newCopyData);
        } elseif ($newCount < $currentCount) {
            $this->removeCopies($currentCount - $newCount);
        }
    }

    private function addCopies(array $copyData): void
    {
        $initials = '';
        foreach (explode(' ', $this->book->title) as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }

        $lastCopyNumber = 0;
        $lastCopy = Copy::where('book_id', $this->book->id)
            ->orderBy('copy_id', 'desc')
            ->first();

        if ($lastCopy) {
            preg_match('/(\d+)$/', $lastCopy->copy_id, $matches);
            if (isset($matches[1])) {
                $lastCopyNumber = (int) $matches[1];
            }
        }

        foreach ($copyData as $i => $row) {
            $copyId = $initials . $this->book->department_id . str_pad($lastCopyNumber + $i + 1, 3, '0', STR_PAD_LEFT);

            $copy = Copy::create([
                'copy_id'   => $copyId,
                'book_id'   => $this->book->id,
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

    private function removeCopies(int $count): void
    {
        $copiesToRemove = Copy::where('book_id', $this->book->id)
            ->orderBy('copy_id', 'desc')
            ->limit($count)
            ->get();

        foreach ($copiesToRemove as $copy) {
            if ($copy->status !== 'Available') {
                throw new \Exception("Cannot remove copies that are currently borrowed. Copy ID: {$copy->copy_id}");
            }
        }

        Copy::where('book_id', $this->book->id)
            ->orderBy('copy_id', 'desc')
            ->limit($count)
            ->delete();
    }

    private function generateUniqueRefNumber(): string
    {
        do {
            $random = strtoupper(Str::random(15));
            $random = preg_replace('/[^A-Z0-9]/', '', $random);
            while (strlen($random) < 15) {
                $random .= strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 1));
            }
            $refNumber = 'BCU-EDITBK-' . substr($random, 0, 15);
        } while (LibraryTransaction::where('ref_number', $refNumber)->exists());

        return $refNumber;
    }

    public function closeModal(): void
    {
        $this->dispatch('closeEdit');
    }

    public function render()
    {
        return view('livewire.pages.books.book-edit');
    }
}
