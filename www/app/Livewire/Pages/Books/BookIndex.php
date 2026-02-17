<?php

namespace App\Livewire\Pages\Books;

use Livewire\WithPagination;
use App\Models\Book;
use App\Models\Department;
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
            'books' => $books,
            'departments' => $departments,
        ]);
    }
}
