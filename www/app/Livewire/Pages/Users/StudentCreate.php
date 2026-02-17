<?php

namespace App\Livewire\Pages\Users;

use Livewire\Component;
use App\Models\Student;
use App\Models\Department;
use App\Models\Course;
use App\Models\LibraryTransaction;
use Illuminate\Support\Str;

class StudentCreate extends Component
{
    public $student_id;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $department_id = null;
    public $course_id = null;
    public $year_level = 1;

    public $departments;
    public $filteredCourses = [];

    protected $rules = [
        'student_id' => 'required|string|max:50|unique:students,student_id',
        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'required|string|max:255',
        'department_id' => 'required|exists:departments,department_code',
        'course_id' => 'required|exists:courses,course_code',
        'year_level' => 'required|integer|min:1|max:5',
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

    public function saveStudent()
    {
        $this->validate();

        try {
            Student::create([
                'student_id' => $this->student_id,
                'first_name' => $this->first_name,
                'middle_name' => $this->middle_name,
                'last_name' => $this->last_name,
                'department_id' => $this->department_id,
                'course_id' => $this->course_id,
                'year_level' => $this->year_level,
            ]);

            $refNumber = $this->generateUniqueRefNumber();

            LibraryTransaction::create([
                'transaction_name' => 'Add Student',
                'ref_number' => $refNumber,
            ]);

            $this->reset([
                'student_id',
                'first_name',
                'middle_name',
                'last_name',
                'department_id',
                'course_id',
                'year_level'
            ]);
            $this->filteredCourses = [];

            $this->dispatch('studentCreated');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to add student: ' . $e->getMessage());
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

            $refNumber = 'ADDSTDNT-' . $randomString;
        } while (LibraryTransaction::where('ref_number', $refNumber)->exists());

        return $refNumber;
    }

    public function closeModal()
    {
        $this->dispatch('closeCreate');
    }

    public function render()
    {
        return view('livewire.pages.users.student-create');
    }
}
