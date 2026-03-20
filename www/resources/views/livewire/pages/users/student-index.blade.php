<div class="w-full flex flex-col gap-4 p-3" x-on:close-modal.window="$flux.modal($event.detail.name).close()">
    <x-flash />

    <div>
        <flux:heading size="xl">Student Manager</flux:heading>
        <flux:text>A table to manage students using the library <flux:text size="xs">(Add, Edit, and Archive Student)
            </flux:text>
        </flux:text>
    </div>

    <div class="flex flex-col gap-4">
        <div class="flex gap-4">
            <flux:input icon="magnifying-glass" type="search" wire:model.live="search"
                placeholder="Search students..." />

            <flux:modal.trigger name="create-student">
                <flux:button icon="user-plus" variant="primary" color="amber"
                    x-on:click="$flux.modal('create-student').show()">Add Student</flux:button>
            </flux:modal.trigger>

            <flux:dropdown>
                <flux:button icon:trailing="chevron-down">
                    {{ $department ? ($departments->firstWhere('department_code', $department)?->name ?? 'Department') : 'All Departments' }}
                </flux:button>
                <flux:menu>
                    <flux:menu.item wire:click="$set('department', '')">All Departments</flux:menu.item>
                    <flux:menu.separator />
                    @foreach($departments as $dept)
                        <flux:menu.item wire:click="$set('department', '{{ $dept->department_code }}')">
                            {{ $dept->name }}
                        </flux:menu.item>
                    @endforeach
                </flux:menu>
            </flux:dropdown>

            <flux:dropdown>
                <flux:button icon:trailing="chevron-down">
                    {{ $course ? ($courses->firstWhere('course_code', $course)?->name ?? 'Course') : 'All Courses' }}
                </flux:button>
                <flux:menu>
                    <flux:menu.item wire:click="$set('course', '')">All Courses</flux:menu.item>
                    <flux:menu.separator />
                    @foreach($courses as $crs)
                        <flux:menu.item wire:click="$set('course', '{{ $crs->course_code }}')">
                            {{ $crs->name }}
                        </flux:menu.item>
                    @endforeach
                </flux:menu>
            </flux:dropdown>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Student ID</flux:table.column>
                <flux:table.column>Full Name</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Course</flux:table.column>
                <flux:table.column>Year Level</flux:table.column>
                <flux:table.column>Active Issuance</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($students as $st)
                    <flux:table.row>
                        <flux:table.cell>{{ $st->student_id }}</flux:table.cell>
                        <flux:table.cell>{{ $st->full_name }}</flux:table.cell>
                        <flux:table.cell>{{ $st->department->name ?? 'N/A' }}</flux:table.cell>
                        <flux:table.cell>{{ $st->course->name ?? 'N/A' }}</flux:table.cell>
                        <flux:table.cell>{{ $st->year_level }}</flux:table.cell>
                        <flux:table.cell>
                            @php
    $activeBorrows = $st->borrows()
        ->whereNull('date_returned')
        ->with('copy.book')
        ->get();
                            @endphp
                            @if($activeBorrows->isNotEmpty())
                                <div class="flex flex-col gap-1">
                                    @foreach($activeBorrows as $borrow)
                                        <flux:badge color="amber" size="sm">
                                            {{ $borrow->copy->book->title ?? 'Unknown' }}
                                        </flux:badge>
                                    @endforeach
                                </div>
                            @else
                                <flux:badge color="zinc">None</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <div class="flex gap-2 justify-end">
                                <flux:modal.trigger name="edit-student-{{ $st->student_id }}">
                                    <flux:button icon="eye"
                                        x-on:click="$flux.modal('edit-student-{{ $st->student_id }}').show()" />
                                </flux:modal.trigger>
                                <flux:button icon="archive-box-arrow-down" variant="danger"
                                    x-on:click="$flux.modal('archive-student').show(); $wire.set('archivingId', '{{ $st->student_id }}')" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>

                    <flux:modal name="edit-student-{{ $st->student_id }}"
                        class="flex flex-col gap-4 p-4 rounded-lg bg-white dark:bg-zinc-800 border border-solid border-zinc-600">
                        <livewire:pages.users.student-edit :student-id="$st->student_id"
                            wire:key="student-edit-{{ $st->student_id }}" />
                    </flux:modal>

                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" align="center">No Students Found</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-pagination :paginator="$students" />
    </div>

    <x-confirm-modal name="archive-student" title="Archive Student"
        description="This will remove them from the active student list. Continue?" confirm-label="Archive"
        confirm-variant="danger" confirm-action="archiveConfirmed" />

    <flux:modal name="create-student"
        class="flex flex-col gap-4 p-4 rounded-lg bg-white dark:bg-zinc-800 border border-solid border-zinc-600">
        <livewire:pages.users.student-create />
    </flux:modal>
</div>
