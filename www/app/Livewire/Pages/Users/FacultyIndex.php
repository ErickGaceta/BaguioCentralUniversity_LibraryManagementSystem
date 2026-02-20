<?php

namespace App\Livewire\Pages\Users;

use App\Models\Faculty;
use App\Models\Department;
use App\Services\ArchiveTransactionService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;

#[Lazy] class FacultyIndex extends Component
{
    use WithPagination;

    public $perPage = 20;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingFacultyId = null;

    public $search = '';
    public string $department = '';

    #[On('facultyCreated')]
    public function handleFacultyCreated()
    {
        $this->closeCreateModal();
        session()->flash('message', 'Faculty has been added to the system.');
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

    #[On('facultyUpdated')]
    public function handleFacultyEdited()
    {
        $this->closeEditModal();
        session()->flash('message', 'Faculty information has been updated.');
    }

    #[On('closeEdit')]
    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingFacultyId = null;
    }

    public function openEditModal($facultyId)
    {
        $this->editingFacultyId = $facultyId;
        $this->showEditModal = true;
    }

    public function archiveFaculty($facultyId)
    {
        $faculty = Faculty::find($facultyId);

        if (!$faculty) {
            session()->flash('message', 'Faculty member not found.');
            return;
        }

        // Prevent archiving if there are active borrows
        $activeBorrows = $faculty->borrows()->whereNull('date_returned')->count();
        if ($activeBorrows > 0) {
            session()->flash('message', 'Cannot archive faculty with active book issuances. Please return all books first.');
            return;
        }

        ArchiveTransactionService::record('faculty', "{$faculty->full_name} ({$faculty->faculty_id})");

        $faculty->delete();

        session()->flash('message', 'Faculty member has been archived.');
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

        $faculties = Faculty::with([
            'department',
            'borrows' => function ($query) {
                $query->whereNull('date_returned')
                    ->with('copy.book');
            }
        ])
            ->when($this->department, function ($q) {
                $q->where('department_id', $this->department);
            })
            ->where(function ($q) {
                $q->where('faculty_id', 'like', '%' . $this->search . '%')
                    ->orWhere('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('middle_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%');
            })
            ->paginate($this->perPage);

        return view('livewire.pages.users.faculty-index', [
            'faculties'   => $faculties,
            'departments' => $departments,
        ]);
    }
}
