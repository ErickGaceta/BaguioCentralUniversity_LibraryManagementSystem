<?php

namespace App\Livewire\Pages\Archives;

use App\Models\Student;
use App\Models\Faculty;
use App\Models\StudentArchive;
use App\Models\FacultyArchive;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;

#[Lazy] class Users extends Component
{
    use WithPagination;

    public $userType = 'students';

    // Restore state
    public ?int    $restoringId       = null;
    public ?string $restoringUserType = null;

    // Delete state
    public ?int    $deletingId       = null;
    public ?string $deletingUserType = null;
    public array   $selectedIds      = [];
    public bool    $selectAll        = false;

    // ── Filter ────────────────────────────────────────────────────────────────

    public function updatedUserType(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll   = false;
    }

    // ── Restore ───────────────────────────────────────────────────────────────

    public function restoreConfirmed(): void
    {
        if (!$this->restoringId || !$this->restoringUserType) return;
        $this->restoreUser($this->restoringId, $this->restoringUserType);
        $this->restoringId       = null;
        $this->restoringUserType = null;
    }

    public function restoreUser(int $userId, string $type): void
    {
        if ($type === 'student') {
            $archivedUser = StudentArchive::findOrFail($userId);

            Student::create([
                'student_id'    => $archivedUser->student_id,
                'first_name'    => $archivedUser->first_name,
                'middle_name'   => $archivedUser->middle_name,
                'last_name'     => $archivedUser->last_name,
                'department_id' => $archivedUser->department_id,
                'course_id'     => $archivedUser->course_id,
                'year_level'    => $archivedUser->year_level,
            ]);

            $archivedUser->delete();
            session()->flash('message', 'Student restored successfully!');
        } elseif ($type === 'faculty') {
            $archivedUser = FacultyArchive::findOrFail($userId);

            Faculty::create([
                'faculty_id'    => $archivedUser->faculty_id,
                'first_name'    => $archivedUser->first_name,
                'middle_name'   => $archivedUser->middle_name,
                'last_name'     => $archivedUser->last_name,
                'department_id' => $archivedUser->department_id,
                'occupation'    => $archivedUser->occupation,
            ]);

            $archivedUser->delete();
            session()->flash('message', 'Faculty restored successfully!');
        }
    }

    // ── Single Delete ─────────────────────────────────────────────────────────

    public function confirmDelete(int $id, string $type): void
    {
        $this->deletingId       = $id;
        $this->deletingUserType = $type;
        $this->dispatch('open-modal', name: 'delete-user');
    }

    public function deleteConfirmed(): void
    {
        if (!$this->deletingId || !$this->deletingUserType) return;

        $modelClass = $this->deletingUserType === 'student'
            ? StudentArchive::class
            : FacultyArchive::class;

        $modelClass::findOrFail($this->deletingId)->delete();

        $this->deletingId       = null;
        $this->deletingUserType = null;
        $this->selectedIds      = [];

        session()->flash('message', 'User permanently deleted.');
    }

    // ── Bulk Delete ───────────────────────────────────────────────────────────

    public function confirmDeleteSelected(): void
    {
        if (empty($this->selectedIds)) return;
        $this->dispatch('open-modal', name: 'delete-users-bulk');
    }

    public function deleteSelectedConfirmed(): void
    {
        if (empty($this->selectedIds)) return;

        $modelClass = $this->userType === 'students'
            ? StudentArchive::class
            : FacultyArchive::class;

        $modelClass::whereIn('id', $this->selectedIds)->delete();

        $count = count($this->selectedIds);
        $this->selectedIds = [];
        $this->selectAll   = false;

        session()->flash('message', "{$count} user(s) permanently deleted.");
    }

    // ── Select-all helper ─────────────────────────────────────────────────────

    public function updatedSelectAll(bool $value): void
    {
        if (!$value) {
            $this->selectedIds = [];
            return;
        }

        $modelClass = $this->userType === 'students'
            ? StudentArchive::class
            : FacultyArchive::class;

        $this->selectedIds = $modelClass::pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    // ── Placeholder / Render ──────────────────────────────────────────────────

    public function placeholder()
    {
        return <<<'HTML'
            <div class="w-full h-full flex justify-center items-center align-center">
                <span class="loader"></span>
            </div>
        HTML;
    }

    public function render()
    {
        $users = $this->userType === 'students'
            ? StudentArchive::with(['department', 'course'])->orderBy('created_at', 'desc')->paginate(10)
            : FacultyArchive::with('department')->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.pages.archives.users', [
            'users' => $users,
        ]);
    }
}
