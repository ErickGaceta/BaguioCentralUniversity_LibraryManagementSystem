<div class="w-full flex flex-col gap-4 p-3">
    <x-flash />

    <div>
        <flux:heading size="xl">Users Archive</flux:heading>
        <flux:text>Archived students and faculty members</flux:text>
    </div>

    <div class="flex flex-col gap-4">

        {{-- Type toggle --}}
        <div class="flex gap-2">
            <flux:button wire:click="$set('userType', 'students')"
                :variant="$userType === 'students' ? 'primary' : 'ghost'">Students</flux:button>
            <flux:button wire:click="$set('userType', 'faculties')"
                :variant="$userType === 'faculties' ? 'primary' : 'ghost'">Faculties</flux:button>
        </div>

        {{-- Bulk-action toolbar --}}
        @if(!empty($selectedIds))
            <div
                class="flex items-center gap-3 px-3 py-2 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                <flux:text class="text-sm font-medium text-red-700 dark:text-red-300">
                    {{ count($selectedIds) }} user(s) selected
                </flux:text>
                <flux:spacer />
                <flux:button variant="danger" size="sm" icon="trash" wire:click="confirmDeleteSelected">
                    Delete Selected
                </flux:button>
            </div>
        @endif

        @if($userType === 'students')
            {{-- ── Students ── --}}
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>
                        <input type="checkbox" wire:model.live="selectAll"
                            class="rounded border-zinc-300 dark:border-zinc-600" />
                    </flux:table.column>
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
                            <flux:table.cell>
                                <input type="checkbox" value="{{ $user->id }}" wire:model.live="selectedIds"
                                    class="rounded border-zinc-300 dark:border-zinc-600" />
                            </flux:table.cell>
                            <flux:table.cell>{{ $user->student_id }}</flux:table.cell>
                            <flux:table.cell>{{ $user->full_name }}</flux:table.cell>
                            <flux:table.cell>{{ $user->department->name ?? 'N/A' }}</flux:table.cell>
                            <flux:table.cell>{{ $user->course->name ?? 'N/A' }}</flux:table.cell>
                            <flux:table.cell>{{ $user->year_level }}</flux:table.cell>
                            <flux:table.cell>{{ $user->created_at->format('M d, Y') }}</flux:table.cell>
                            <flux:table.cell align="end">
                                <div class="flex gap-1 justify-end">
                                    {{-- Restore --}}
                                    <flux:button icon="arrow-uturn-left" size="sm"
                                        x-on:click="$flux.modal('restore-user').show(); $wire.set('restoringId', {{ $user->id }}); $wire.set('restoringUserType', 'student')" />

                                    {{-- Single permanent delete --}}
                                    <flux:button icon="trash" size="sm" variant="danger"
                                        wire:click="confirmDelete({{ $user->id }}, 'student')" />
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="8" class="text-center">No Archived Students Found</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>

        @else
            {{-- ── Faculties ── --}}
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>
                        <input type="checkbox" wire:model.live="selectAll"
                            class="rounded border-zinc-300 dark:border-zinc-600" />
                    </flux:table.column>
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
                            <flux:table.cell>
                                <input type="checkbox" value="{{ $user->id }}" wire:model.live="selectedIds"
                                    class="rounded border-zinc-300 dark:border-zinc-600" />
                            </flux:table.cell>
                            <flux:table.cell>{{ $user->faculty_id }}</flux:table.cell>
                            <flux:table.cell>{{ $user->full_name }}</flux:table.cell>
                            <flux:table.cell>{{ $user->department->name ?? 'N/A' }}</flux:table.cell>
                            <flux:table.cell>{{ $user->occupation }}</flux:table.cell>
                            <flux:table.cell>{{ $user->created_at->format('M d, Y') }}</flux:table.cell>
                            <flux:table.cell align="end">
                                <div class="flex gap-1 justify-end">
                                    {{-- Restore --}}
                                    <flux:button icon="arrow-uturn-left" size="sm"
                                        x-on:click="$flux.modal('restore-user').show(); $wire.set('restoringId', {{ $user->id }}); $wire.set('restoringUserType', 'faculty')" />

                                    {{-- Single permanent delete --}}
                                    <flux:button icon="trash" size="sm" variant="danger"
                                        wire:click="confirmDelete({{ $user->id }}, 'faculty')" />
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="7" class="text-center">No Archived Faculty Found</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        @endif

        {{ $users->links() }}
    </div>

    {{-- Restore modal (existing) --}}
    <x-restore-modal name="restore-user" title="Restore User"
        description="Are you sure you want to restore this user? They will be moved back to the active users list."
        confirm-action="restoreConfirmed" />

    {{-- Single delete confirmation modal --}}
    <flux:modal name="delete-user" class="max-w-sm">
        <div class="flex flex-col gap-4 p-1">
            <div>
                <flux:heading size="lg">Permanently Delete User</flux:heading>
                <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    This action is <strong>irreversible</strong>. The user will be removed from the archive permanently
                    and cannot be recovered.
                </flux:text>
            </div>
            <div class="flex justify-end gap-2">
                <flux:button x-on:click="$flux.modal('delete-user').close()">Cancel</flux:button>
                <flux:button variant="danger" wire:click="deleteConfirmed"
                    x-on:click="$flux.modal('delete-user').close()">
                    Delete Permanently
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Bulk delete confirmation modal --}}
    <flux:modal name="delete-users-bulk" class="max-w-sm">
        <div class="flex flex-col gap-4 p-1">
            <div>
                <flux:heading size="lg">Permanently Delete Selected Users</flux:heading>
                <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    You are about to permanently delete <strong>{{ count($selectedIds) }} user(s)</strong>.
                    This action is <strong>irreversible</strong> and cannot be undone.
                </flux:text>
            </div>
            <div class="flex justify-end gap-2">
                <flux:button x-on:click="$flux.modal('delete-users-bulk').close()">Cancel</flux:button>
                <flux:button variant="danger" wire:click="deleteSelectedConfirmed"
                    x-on:click="$flux.modal('delete-users-bulk').close()">
                    Delete {{ count($selectedIds) }} User(s)
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
