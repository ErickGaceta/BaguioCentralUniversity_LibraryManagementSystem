<?php

namespace App\Livewire\Pages\Archives;

use App\Models\StudentArchive;
use App\Models\FacultyArchive;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    public $userType = 'students'; // students or faculties
    public $showRestoreModal = false;
    public $restoringUserId = null;
    public $restoringUserType = null;

    public function updatedUserType()
    {
        $this->resetPage();
    }

    public function openRestoreModal($userId, $type)
    {
        $this->restoringUserId = $userId;
        $this->restoringUserType = $type;
        $this->showRestoreModal = true;
    }

    public function closeRestoreModal()
    {
        $this->showRestoreModal = false;
        $this->restoringUserId = null;
        $this->restoringUserType = null;
    }

    public function restoreUser()
    {
        if ($this->restoringUserType === 'student') {
            $archivedUser = StudentArchive::findOrFail($this->restoringUserId);

            \App\Models\Student::create([
                'student_id' => $archivedUser->student_id,
                'first_name' => $archivedUser->first_name,
                'middle_name' => $archivedUser->middle_name,
                'last_name' => $archivedUser->last_name,
                'department_id' => $archivedUser->department_id,
                'course_id' => $archivedUser->course_id,
                'year_level' => $archivedUser->year_level,
            ]);

            $archivedUser->delete();
            session()->flash('message', 'Student restored successfully!');
        } elseif ($this->restoringUserType === 'faculty') {
            $archivedUser = FacultyArchive::findOrFail($this->restoringUserId);

            \App\Models\Faculty::create([
                'faculty_id' => $archivedUser->faculty_id,
                'first_name' => $archivedUser->first_name,
                'middle_name' => $archivedUser->middle_name,
                'last_name' => $archivedUser->last_name,
                'department_id' => $archivedUser->department_id,
                'occupation' => $archivedUser->occupation,
            ]);

            $archivedUser->delete();
            session()->flash('message', 'Faculty restored successfully!');
        }

        $this->closeRestoreModal();
    }

    public function render()
    {
        if ($this->userType === 'students') {
            $users = StudentArchive::with(['department', 'course'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            $users = FacultyArchive::with('department')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('livewire.pages.archives.users', [
            'users' => $users,
        ]);
    }
}
