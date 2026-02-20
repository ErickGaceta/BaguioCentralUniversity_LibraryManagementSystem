<?php

namespace App\Livewire\Pages\Copies;

use App\Models\Copy;
use App\Models\CopyAccession;
use App\Models\Course;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

use Livewire\Attributes\Lazy;

#[Lazy] class CopyIndex extends Component
{
    use WithPagination;

    public $perPage = 20;
    public $showEditModal = false;
    public $editingCopyId = null;

    public string $search = '';
    public string $courseFilter = '';

    // ── Event listeners ───────────────────────────────────────────────────────

    #[On('copyUpdated')]
    public function handleCopyUpdated(): void
    {
        $this->closeEditModal();
        session()->flash('message', 'Copy has been updated.');
    }

    public function placeholder()
    {
        return <<<'HTML'
            <div class="w-full h-full flex justify-center items-center align-center">
                <span class="loader"></span>
            </div>
        HTML;
    }

    #[On('closeEdit')]
    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->editingCopyId = null;
    }

    // ── Modal actions ─────────────────────────────────────────────────────────

    public function openEditModal(string $copyId): void
    {
        $this->editingCopyId = $copyId;
        $this->showEditModal = true;
    }

    // ── Pagination reset ──────────────────────────────────────────────────────

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCourseFilter(): void
    {
        $this->resetPage();
    }

    // ─────────────────────────────────────────────────────────────────────────

    public function render()
    {
        $courses = Course::orderBy('name')->get();

        $copies = Copy::with([
            'book',
            'course',
            'accession',
            'studentBorrows' => function ($query) {
                $query->whereNull('date_returned')->with('student')->latest('date_borrowed');
            },
            'facultyBorrows' => function ($query) {
                $query->whereNull('date_returned')->with('faculty')->latest('date_borrowed');
            },
        ])
            ->when($this->courseFilter, fn($q) => $q->where('course_id', $this->courseFilter))
            ->whereHas('book', function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('author', 'like', '%' . $this->search . '%')
                    ->orWhere('isbn', 'like', '%' . $this->search . '%');
            })
            ->orderBy('copy_id')
            ->paginate($this->perPage);


        return view('livewire.pages.copies.copy-index', [
            'copies'  => $copies,
            'courses' => $courses,
        ]);
    }
}
