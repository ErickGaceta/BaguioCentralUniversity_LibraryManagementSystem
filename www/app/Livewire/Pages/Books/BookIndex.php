<?php

namespace App\Livewire\Pages\Books;

use Livewire\WithPagination;
use App\Models\Book;
use App\Models\Department;
use App\Models\ArchivesLibrary;
use App\Services\ArchiveTransactionService;
use Livewire\Attributes\On;
use Livewire\Component;

class BookIndex extends Component
{
    use WithPagination;

    public $perPage = 20;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingBookId = null;
    public $showNotification = false;
    public $notificationMessage = '';

    public $search = '';

    public string $department = '';

    #[On('bookCreated')]
    public function handleBookCreated()
    {
        $this->closeCreateModal();
        session()->flash('message', 'Book has been added to the library.');
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
        $this->closeEditModal();
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

        ArchivesLibrary::create([
            'book_id'          => $book->id,
            'title'            => $book->title,
            'author'           => $book->author,
            'publication_date' => $book->publication_date,
            'publisher'        => $book->publisher,
            'isbn'             => $book->isbn,
            'department_id'    => $book->department_id,
            'copies'           => $book->copies,
        ]);

        ArchiveTransactionService::record('book', "\"{$book->title}\" by {$book->author}");

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
