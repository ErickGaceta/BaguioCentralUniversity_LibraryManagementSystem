<?php

namespace App\Livewire\Pages\Users;

use Livewire\Component;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\LibraryTransaction;
use Illuminate\Support\Str;

class FacultyCreate extends Component
{
    public $faculty_id;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $department_id = null;
    public $occupation;

    public $departments;

    protected $rules = [
        'faculty_id'    => 'required|string|max:50|unique:faculties,faculty_id',
        'first_name'    => 'required|string|max:255',
        'middle_name'   => 'nullable|string|max:255',
        'last_name'     => 'required|string|max:255',
        'department_id' => 'required|exists:departments,department_code',
        'occupation'    => 'required|string|max:255',
    ];

    public function mount()
    {
        $this->departments = Department::orderBy('name')->get();
    }

    public function saveFaculty()
    {
        $this->validate();

        try {
            Faculty::create([
                'faculty_id'    => $this->faculty_id,
                'first_name'    => $this->first_name,
                'middle_name'   => $this->middle_name,
                'last_name'     => $this->last_name,
                'department_id' => $this->department_id,
                'occupation'    => $this->occupation,
            ]);

            $refNumber = $this->generateUniqueRefNumber();

            LibraryTransaction::create([
                'transaction_name' => 'Add Faculty',
                'ref_number'       => $refNumber,
            ]);

            $this->reset([
                'faculty_id',
                'first_name',
                'middle_name',
                'last_name',
                'department_id',
                'occupation',
            ]);

            $this->dispatch('facultyCreated');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to add faculty: ' . $e->getMessage());
        }
    }

    private function generateUniqueRefNumber(): string
    {
        do {
            $randomString = strtoupper(Str::random(15));
            $randomString = preg_replace('/[^A-Z0-9]/', '', $randomString);
            while (strlen($randomString) < 15) {
                $randomString .= strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 1));
            }
            $randomString = substr($randomString, 0, 15);

            $refNumber = 'ADDFCLTY-' . $randomString;
        } while (LibraryTransaction::where('ref_number', $refNumber)->exists());

        return $refNumber;
    }

    public function closeModal()
    {
        $this->dispatch('closeCreate');
    }

    public function render()
    {
        return view('livewire.pages.users.faculty-create');
    }
}
