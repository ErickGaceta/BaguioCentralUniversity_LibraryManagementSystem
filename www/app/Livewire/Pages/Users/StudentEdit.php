<?php

namespace App\Livewire\Pages\Users;

use Livewire\Component;
use App\Models\Student;
use App\Models\Department;
use App\Models\Course;
use App\Models\LibraryTransaction;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;

#[Lazy] class StudentEdit extends Component
{
    public $studentId;
    public $student;

    public $first_name;
    public $middle_name;
    public $last_name;
    public $department_id = null;
    public $course_id = null;
    public $year_level;

    public $originalData = [];
    public $departments;
    public $filteredCourses = [];

    protected $rules = [
        'first_name'    => 'required|string|max:255',
        'middle_name'   => 'nullable|string|max:255',
        'last_name'     => 'required|string|max:255',
        'department_id' => 'required|exists:departments,department_code',
        'course_id'     => 'required|exists:courses,course_code',
        'year_level'    => 'required|integer|min:1|max:5',
    ];

    public function mount($studentId)
    {
        $this->studentId = $studentId;
        $this->student   = Student::findOrFail($studentId);
        $this->departments = Department::orderBy('name')->get();

        $this->first_name    = $this->student->first_name;
        $this->middle_name   = $this->student->middle_name;
        $this->last_name     = $this->student->last_name;
        $this->department_id = $this->student->department_id;
        $this->course_id     = $this->student->course_id;
        $this->year_level    = $this->student->year_level;

        if ($this->department_id) {
            $this->filteredCourses = Course::where('department_id', $this->department_id)->get();
        }

        $this->originalData = [
            'first_name'    => $this->first_name,
            'middle_name'   => $this->middle_name,
            'last_name'     => $this->last_name,
            'department_id' => $this->department_id,
            'course_id'     => $this->course_id,
            'year_level'    => $this->year_level,
        ];
    }

    public function updatedDepartmentId($value)
    {
        $this->course_id = null;
        $this->filteredCourses = $value
            ? Course::where('department_id', $value)->get()
            : [];
    }

    public function updateStudent()
    {
        $this->validate();

        if (!$this->hasChanges()) {
            session()->flash('info', 'No changes detected.');
            $this->dispatch('studentUpdated');
            return;
        }

        try {
            $this->student->update([
                'first_name'    => $this->first_name,
                'middle_name'   => $this->middle_name,
                'last_name'     => $this->last_name,
                'department_id' => $this->department_id,
                'course_id'     => $this->course_id,
                'year_level'    => $this->year_level,
            ]);

            LibraryTransaction::create([
                'transaction_name' => 'Edit Student',
                'ref_number'       => $this->generateUniqueRefNumber(),
            ]);

            $this->dispatch('studentUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update student: ' . $e->getMessage());
        }
    }

    private function hasChanges(): bool
    {
        foreach ($this->originalData as $key => $value) {
            if ($this->$key != $value) return true;
        }
        return false;
    }

    private function generateUniqueRefNumber(): string
    {
        do {
            $random = strtoupper(preg_replace('/[^A-Z0-9]/', '', Str::random(20)));
            while (strlen($random) < 15) {
                $random .= strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 1));
            }
            $refNumber = 'EDTSTDNT-' . substr($random, 0, 15);
        } while (LibraryTransaction::where('ref_number', $refNumber)->exists());

        return $refNumber;
    }

    public function closeModal()
    {
        $this->dispatch('closeEdit');
    }

    public function render()
    {
        return view('livewire.pages.users.student-edit');
    }
}
