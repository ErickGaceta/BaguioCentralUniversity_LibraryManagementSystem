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
        <flux:heading size="xl">Faculty Manager</flux:heading>
        <flux:text>A table to manage faculty using the library <flux:text size="xs">(Add, Edit, and Archive Faculty)</flux:text>
        </flux:text>
    </div>

    <div class="flex flex-col gap-4">
        <div class="flex gap-4">
            <flux:input icon="magnifying-glass" type="search" wire:model.live="search" placeholder="Search faculty..." />

            <flux:button wire:click="$toggle('showCreateModal')" icon="user-plus" align="end" variant="primary" color="amber">Add Faculty</flux:button>

            <flux:dropdown>
                <flux:button icon:trailing="chevron-down">
                    {{ $department ? ($departments->firstWhere('department_code', $department)?->name ?? 'Department') : 'All Departments' }}
                </flux:button>

                <flux:menu>
                    <flux:menu.item wire:click="$set('department', '')">
                        All Departments
                    </flux:menu.item>

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
                        <flux:button icon="eye" wire:click="openEditModal('{{ $fc->faculty_id }}')" />
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" align="center">
                        No Faculty Found
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-pagination :paginator="$faculties" />
    </div>

    @if($showCreateModal)
    <div class="flex gap-4" style="width: 25vw; z-index: 1000; position: absolute; top: 20%; left: 40%;">
        <livewire:pages.users.faculty-create />
    </div>
    @endif

    @if($showEditModal && $editingFacultyId)
    <div class="flex gap-4" style="width: 25vw; z-index: 1000; position: absolute; top: 20%; left: 40%;">
        <livewire:pages.users.faculty-edit
            :faculty-id="$editingFacultyId"
            wire:key="faculty-edit-{{ $editingFacultyId }}" />
    </div>
    @endif
</div>
