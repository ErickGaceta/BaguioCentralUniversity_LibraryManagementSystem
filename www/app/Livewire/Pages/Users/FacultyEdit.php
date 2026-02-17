<?php

namespace App\Livewire\Pages\Users;

use Livewire\Component;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\LibraryTransaction;
use Illuminate\Support\Str;

class FacultyEdit extends Component
{
    public $facultyId;
    public $faculty;

    public $first_name;
    public $middle_name;
    public $last_name;
    public $department_id = null;
    public $occupation;

    public $originalData = [];
    public $departments;

    protected $rules = [
        'first_name'    => 'required|string|max:255',
        'middle_name'   => 'nullable|string|max:255',
        'last_name'     => 'required|string|max:255',
        'department_id' => 'required|exists:departments,department_code',
        'occupation'    => 'required|string|max:255',
    ];

    public function mount($facultyId)
    {
        $this->facultyId   = $facultyId;
        $this->faculty     = Faculty::findOrFail($facultyId);
        $this->departments = Department::orderBy('name')->get();

        $this->first_name    = $this->faculty->first_name;
        $this->middle_name   = $this->faculty->middle_name;
        $this->last_name     = $this->faculty->last_name;
        $this->department_id = $this->faculty->department_id;
        $this->occupation    = $this->faculty->occupation;

        $this->originalData = [
            'first_name'    => $this->first_name,
            'middle_name'   => $this->middle_name,
            'last_name'     => $this->last_name,
            'department_id' => $this->department_id,
            'occupation'    => $this->occupation,
        ];
    }

    public function updateFaculty()
    {
        $this->validate();

        if (!$this->hasChanges()) {
            session()->flash('info', 'No changes detected.');
            $this->dispatch('facultyUpdated');
            return;
        }

        try {
            $this->faculty->update([
                'first_name'    => $this->first_name,
                'middle_name'   => $this->middle_name,
                'last_name'     => $this->last_name,
                'department_id' => $this->department_id,
                'occupation'    => $this->occupation,
            ]);

            LibraryTransaction::create([
                'transaction_name' => 'Edit Faculty',
                'ref_number'       => $this->generateUniqueRefNumber(),
            ]);

            $this->dispatch('facultyUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update faculty: ' . $e->getMessage());
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
            $refNumber = 'EDTFCLTY-' . substr($random, 0, 15);
        } while (LibraryTransaction::where('ref_number', $refNumber)->exists());

        return $refNumber;
    }

    public function closeModal()
    {
        $this->dispatch('closeEdit');
    }

    public function render()
    {
        return view('livewire.pages.users.faculty-edit');
    }
}
