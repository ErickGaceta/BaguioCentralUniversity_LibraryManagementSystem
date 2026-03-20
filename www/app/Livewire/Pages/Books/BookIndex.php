<?php

namespace App\Livewire\Pages\Books;

use Livewire\WithPagination;
use App\Models\Book;
use App\Models\Copy;
use App\Models\Department;
use App\Models\ArchivesCopy;
use App\Models\ArchivesLibrary;
use App\Services\ArchiveTransactionService;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Lazy;

#[Lazy] class BookIndex extends Component
{
    use WithPagination;

    public $perPage = 20;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingBookId = null;
    public $showNotification = false;
    public $notificationMessage = '';
    public ?int $archivingId = null;
    public $search = '';

    public string $department = '';

    #[On('bookCreated')]
    public function handleBookCreated()
    {
        $this->dispatch('close-modal', name: 'create-book');
        session()->flash('message', 'Book has been added to the library.');
    }

    public function prepareArchive(int $bookId): void
    {
        $this->archivingId = $bookId;
        $this->dispatch('open-archive-modal');
    }

    public function archiveConfirmed(): void
    {
        if (!$this->archivingId) return;

        $this->archiveBook($this->archivingId);
        $this->archivingId = null;
    }

    public function placeholder()
    {
        return <<<'HTML'
            <div class="w-full h-full flex justify-center items-center align-center">
                <span class="loader"></span>
            </div>
        HTML;
    }

    #[On('closeCreate')]
    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
    }

    #[On('bookUpdated')]
    public function handleBookEdited()
    {
        $this->dispatch('close-modal', name: 'edit-book-' . $this->editingBookId);
        session()->flash('message', 'Book changes have been saved.');
    }

    #[On('closeEdit')]
    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingBookId = null;
    }

    public function openEditModal($bookId)
    {
        $this->editingBookId = $bookId;
        $this->showEditModal = true;
    }

    public function archiveBook($bookId)
    {
        $book = Book::find($bookId);

        if (!$book) {
            session()->flash('message', 'Book not found.');
            return;
        }

        // Snapshot the book into archives_library
        $archivedBook = ArchivesLibrary::create([
            'book_id'          => $book->id,
            'title'            => $book->title,
            'author'           => $book->author,
            'publication_date' => $book->publication_date,
            'publisher'        => $book->publisher,
            'isbn'             => $book->isbn,
            'department_id'    => $book->department_id,
            'copies'           => $book->copies,
        ]);

        // Query Copy directly — $book->copies would resolve to the integer
        // column attribute, not a relationship, causing a fatal error
        Copy::where('book_id', $book->id)->each(function ($copy) use ($archivedBook, $book) {
            ArchivesCopy::create([
                'archived_book_id' => $archivedBook->id,
                'original_book_id' => $book->id,
                'copy_id'          => $copy->copy_id,
                'course_id'        => $copy->course_id,
                'status'           => $copy->status,
                'condition'        => $copy->condition,
            ]);
        });

        ArchiveTransactionService::record('book', "\"{$book->title}\" by {$book->author}");

        // Cascade delete removes the book's rows from the live copies table
        $book->delete();

        session()->flash('message', 'Book has been archived.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDepartment()
    {
        $this->resetPage();
    }

    public function render()
    {
        $departments = Department::orderBy('name')->get();

        $books = Book::query()
            ->when($this->department, function ($q) {
                $q->where('department_id', $this->department);
            })
            ->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('author', 'like', '%' . $this->search . '%')
                    ->orWhere('isbn', 'like', '%' . $this->search . '%');
            })
            ->paginate($this->perPage);

        return view('livewire.pages.books.book-index', [
            'books'       => $books,
            'departments' => $departments,
        ]);
    }
}
