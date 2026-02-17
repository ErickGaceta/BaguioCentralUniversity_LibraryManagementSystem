<div class="w-full flex flex-col gap-4 p-3">
    <div style="position: absolute; bottom: 5px; right: 10px;">
        @if(session()->has('message'))
        <div x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 5000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <flux:card class="flex gap-2 align-center items-center justify-center">
                <flux:icon.check-circle class="text-green-500" />
                <flux:separator vertical />
                <div class="flex flex-col">
                    <flux:heading>Success!</flux:heading>
                    <flux:text>{{ session('message') }}</flux:text>
                </div>
            </flux:card>
        </div>
        @endif
    </div>

    <div>
        <flux:heading size="xl">Users Archive</flux:heading>
        <flux:text>Archived students and faculty members</flux:text>
    </div>

    <div class="flex flex-col gap-4">
        <div class="flex gap-2">
            <flux:button
                wire:click="$set('userType', 'students')"
                :variant="$userType === 'students' ? 'primary' : 'ghost'">
                Students
            </flux:button>
            <flux:button
                wire:click="$set('userType', 'faculties')"
                :variant="$userType === 'faculties' ? 'primary' : 'ghost'">
                Faculties
            </flux:button>
        </div>

        @if($userType === 'students')
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Student ID</flux:table.column>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Course</flux:table.column>
                <flux:table.column>Year Level</flux:table.column>
                <flux:table.column>Archived Date</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($users as $user)
                <flux:table.row>
                    <flux:table.cell>{{ $user->student_id }}</flux:table.cell>
                    <flux:table.cell>{{ $user->full_name }}</flux:table.cell>
                    <flux:table.cell>{{ $user->department->name ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell>{{ $user->course->name ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell>{{ $user->year_level }}</flux:table.cell>
                    <flux:table.cell>{{ $user->created_at->format('M d, Y') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button icon="arrow-uturn-left" size="sm" wire:click="openRestoreModal({{ $user->id }}, 'student')" />
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center">
                        No Archived Students Found
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @else
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Faculty ID</flux:table.column>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Occupation</flux:table.column>
                <flux:table.column>Archived Date</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($users as $user)
                <flux:table.row>
                    <flux:table.cell>{{ $user->faculty_id }}</flux:table.cell>
                    <flux:table.cell>{{ $user->full_name }}</flux:table.cell>
                    <flux:table.cell>{{ $user->department->name ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell>{{ $user->occupation }}</flux:table.cell>
                    <flux:table.cell>{{ $user->created_at->format('M d, Y') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button icon="arrow-uturn-left" size="sm" wire:click="openRestoreModal({{ $user->id }}, 'faculty')" />
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center">
                        No Archived Faculty Found
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        @endif

        {{ $users->links() }}
    </div>

    @if($showRestoreModal)
    <flux:modal wire:model="showRestoreModal">
        <flux:heading>Restore {{ ucfirst($restoringUserType) }}</flux:heading>
        <flux:text>
            Are you sure you want to restore this {{ $restoringUserType }}? They will be moved back to the active users list.
        </flux:text>
        <div>
            <flux:button wire:click="closeRestoreModal" variant="ghost">Cancel</flux:button>
            <flux:button wire:click="restoreUser" variant="primary">Restore</flux:button>
        </div>
    </flux:modal>
    @endif
</div>
