<?php

namespace App\Livewire\Pages\Users;

use App\Models\Student;
use App\Models\Department;
use App\Models\Course;
use App\Models\StudentArchive;
use App\Services\ArchiveTransactionService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;

#[Lazy] class StudentIndex extends Component
{
    use WithPagination;

    public $perPage = 20;

    public $search = '';
    public string $department = '';
    public string $course = '';
    public string $yearLevel = '';

    #[On('studentCreated')]
    public function handleStudentCreated()
    {
        $this->dispatch('close-modal', name: 'create-student');
        session()->flash('message', 'Student has been added to the system.');
    }

    public ?string $archivingId = null;

    public function archiveConfirmed(): void
    {
        if (!$this->archivingId) return;

        $this->archiveStudent($this->archivingId);

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

    #[On('studentUpdated')]
    public function handleStudentEdited(string $studentId)
    {
        $this->dispatch('close-modal', name: 'edit-student-' . $studentId);
        session()->flash('message', 'Student information has been updated.');
    }

    public function archiveStudent($studentId)
    {
        $student = Student::find($studentId);

        if (!$student) {
            session()->flash('message', 'Student not found.');
            return;
        }

        $activeBorrows = $student->borrows()->whereNull('date_returned')->count();
        if ($activeBorrows > 0) {
            session()->flash('message', 'Cannot archive student with active book issuances. Please return all books first.');
            return;
        }

        StudentArchive::create([
            'student_id'    => $student->student_id,
            'first_name'    => $student->first_name,
            'middle_name'   => $student->middle_name,
            'last_name'     => $student->last_name,
            'department_id' => $student->department_id,
            'course_id'     => $student->course_id,
            'year_level'    => $student->year_level,
            'archived_at'   => now(),
        ]);

        ArchiveTransactionService::record('student', "{$student->full_name} ({$student->student_id})");

        $student->delete();

        session()->flash('message', 'Student has been archived.');
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
            'students'    => $students,
            'departments' => $departments,
            'courses'     => $courses,
        ]);
    }
}
