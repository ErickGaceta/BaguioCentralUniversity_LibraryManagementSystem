<div class="w-full flex flex-col gap-4 p-3">

    <div style="position: fixed; bottom: 5px; right: 10px;" class="bg-white dark:bg-zinc-700 z-1000 rounded-sm">
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
            <flux:card class="flex gap-2 align-center items-center justify-center z-1000">
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
        <flux:heading size="xl">Copy Browser</flux:heading>
        <flux:text>List of all book copies</flux:text>
    </div>

    <div class="flex flex-col gap-2">
        <div class="flex gap-4">
            <flux:input
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by title, author, or ISBN..." />

            <flux:dropdown>
                <flux:button icon:trailing="chevron-down">
                    {{ $courseFilter
                        ? ($courses->firstWhere('course_code', $courseFilter)?->name ?? 'Course')
                        : 'All Courses' }}
                </flux:button>

                <flux:menu>
                    <flux:menu.item wire:click="$set('courseFilter', '')">All Courses</flux:menu.item>
                    <flux:menu.separator />
                    @foreach($courses as $course)
                    <flux:menu.item wire:click="$set('courseFilter', '{{ $course->course_code }}')">
                        {{ $course->name }}
                    </flux:menu.item>
                    @endforeach
                </flux:menu>
            </flux:dropdown>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Book Title</flux:table.column>
                <flux:table.column>Copy ID</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Course</flux:table.column>
                <flux:table.column>Condition</flux:table.column>
                <flux:table.column>Issued To</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($copies as $copy)
                <flux:table.row>
                    <flux:table.cell>{{ $copy->book?->title ?? '—' }}</flux:table.cell>
                    <flux:table.cell>{{ $copy->copy_id }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge
                            color="{{ $copy->status === 'Available' ? 'green' : 'yellow' }}"
                            size="sm">
                            {{ $copy->status }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $copy->course?->name ?? $copy->course_id ?? '—' }}
                    </flux:table.cell>
                    <flux:table.cell>{{ $copy->condition ?? '—' }}</flux:table.cell>
                    <flux:table.cell>
                        @php
                        $studentBorrow = $copy->studentBorrows->first();
                        $facultyBorrow = $copy->facultyBorrows->first();
                        @endphp

                        @if($studentBorrow?->student)
                        {{ $studentBorrow->student->full_name }} &mdash; Student
                        @elseif($facultyBorrow?->faculty)
                        {{ $facultyBorrow->faculty->full_name }} &mdash; Faculty
                        @else
                        &mdash;
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button
                            icon="eye"
                            wire:click="openEditModal('{{ $copy->copy_id }}')" />
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="7">No copies found.</flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-pagination :paginator="$copies" />
    </div>

    @if($showEditModal && $editingCopyId)
    <div class="flex gap-4" style="width: 25vw; z-index: 1000; position: fixed; top: 20%; left: 40%;">
        <livewire:pages.copies.copy-show
            :copy-id="$editingCopyId"
            wire:key="copy-show-{{ $editingCopyId }}" />
    </div>
    @endif

</div>
