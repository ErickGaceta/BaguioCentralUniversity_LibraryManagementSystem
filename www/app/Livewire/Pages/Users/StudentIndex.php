<?php

namespace App\Livewire\Pages\Users;

use App\Models\Student;
use App\Models\Department;
use App\Models\Course;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class StudentIndex extends Component
{
    use WithPagination;

    public $perPage = 20;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingStudentId = null;

    public $search = '';
    public string $department = '';
    public string $course = '';
    public string $yearLevel = '';

    #[On('studentCreated')]
    public function handleStudentCreated()
    {
        $this->closeCreateModal();
        session()->flash('message', 'Student has been added to the system.');
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

    #[On('studentUpdated')]
    public function handleStudentEdited()
    {
        $this->closeEditModal();
        session()->flash('message', 'Student information has been updated.');
    }

    #[On('closeEdit')]
    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingStudentId = null;
    }

    public function openEditModal($studentId)
    {
        $this->editingStudentId = $studentId;
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

    public function updatingCourse()
    {
        $this->resetPage();
    }

    public function updatingYearLevel()
    {
        $this->resetPage();
    }

    public function render()
    {
        $departments = Department::orderBy('name')->get();
        $courses = Course::orderBy('name')->get();

        $students = Student::with([
            'department',
            'course',
            'borrows' => function ($query) {
                $query->whereNull('date_returned')
                    ->with('copy.book');
            }
        ])
            ->when($this->department, function ($q) {
                $q->where('department_id', $this->department);
            })
            ->when($this->course, function ($q) {
                $q->where('course_id', $this->course);
            })
            ->when($this->yearLevel, function ($q) {
                $q->where('year_level', $this->yearLevel);
            })
            ->where(function ($q) {
                $q->where('student_id', 'like', '%' . $this->search . '%')
                    ->orWhere('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('middle_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%');
            })
            ->paginate($this->perPage);

        return view('livewire.pages.users.student-index', [
            'students' => $students,
            'departments' => $departments,
            'courses' => $courses,
        ]);
    }
}
