<div class="w-full flex flex-col gap-4 p-3">
    <x-flash />

    <div>
        <flux:heading size="xl">Faculty Manager</flux:heading>
        <flux:text>A table to manage faculty using the library <flux:text size="xs">(Add, Edit, and Archive Faculty)
            </flux:text>
        </flux:text>
    </div>

    <div class="flex flex-col gap-4">
        <div class="flex gap-4">
            <flux:input icon="magnifying-glass" type="search" wire:model.live="search"
                placeholder="Search faculty..." />

            <flux:modal.trigger name="create-faculty">
                <flux:button icon="user-plus" variant="primary" color="amber"
                    x-on:click="$flux.modal('create-faculty').show()">Add Faculty</flux:button>
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
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Faculty ID</flux:table.column>
                <flux:table.column>Full Name</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Occupation</flux:table.column>
                <flux:table.column>Active Issuance</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($faculties as $fc)
                    <flux:table.row>
                        <flux:table.cell>{{ $fc->faculty_id }}</flux:table.cell>
                        <flux:table.cell>{{ $fc->full_name }}</flux:table.cell>
                        <flux:table.cell>{{ $fc->department->name ?? 'N/A' }}</flux:table.cell>
                        <flux:table.cell>{{ $fc->occupation }}</flux:table.cell>
                        <flux:table.cell>
                            @php
                                $activeBorrows = $fc->borrows()
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
                                <flux:modal.trigger name="edit-faculty-{{ $fc->faculty_id }}">
                                    <flux:button icon="eye"
                                        x-on:click="$flux.modal('edit-faculty-{{ $fc->faculty_id }}').show()" />
                                </flux:modal.trigger>
                                <flux:button icon="archive-box-arrow-down" variant="danger"
                                    x-on:click="$flux.modal('archive-faculty').show(); $wire.set('archivingId', '{{ $fc->faculty_id }}')" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>

                    <flux:modal name="edit-faculty-{{ $fc->faculty_id }}"
                        class="flex flex-col gap-4 p-4 rounded-lg bg-white dark:bg-zinc-800 border border-solid border-zinc-600">
                        <livewire:pages.users.faculty-edit :faculty-id="$fc->faculty_id"
                            wire:key="faculty-edit-{{ $fc->faculty_id }}" />
                    </flux:modal>

                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" align="center">No Faculty Found</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-pagination :paginator="$faculties" />
    </div>

    <x-confirm-modal name="archive-faculty" title="Archive Faculty"
        description="This will remove them from the active faculty list. Continue?" confirm-label="Archive"
        confirm-variant="danger" confirm-action="archiveConfirmed" />

    <flux:modal name="create-faculty"
        class="flex flex-col gap-4 p-4 rounded-lg bg-white dark:bg-zinc-800 border border-solid border-zinc-600">
        <livewire:pages.users.faculty-create />
    </flux:modal>
</div>
