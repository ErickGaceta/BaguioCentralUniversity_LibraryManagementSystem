<div class="flex flex-col gap-4 p-4 rounded-lg bg-white dark:bg-zinc-800 border border-solid border-zinc-600"
    style="width: 100%; max-width: 800px;">

    <div class="flex justify-between items-center">
        <flux:heading size="lg">Copy Details — {{ $copy->copy_id }}</flux:heading>
        <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="closeModal" />
    </div>

    <flux:separator />

    @if(session()->has('error'))
    <flux:callout variant="danger" icon="exclamation-circle">
        <flux:callout.heading>Error</flux:callout.heading>
        <flux:callout.text>{{ session('error') }}</flux:callout.text>
    </flux:callout>
    @endif

    <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col gap-2">
            <flux:text><span class="font-semibold">Title:</span> {{ $copy->book->title }}</flux:text>
            <flux:text><span class="font-semibold">Author:</span> {{ $copy->book->author }}</flux:text>
            <flux:text><span class="font-semibold">ISBN:</span> {{ $copy->book->isbn ?? '—' }}</flux:text>
            <flux:text><span class="font-semibold">Accession Number:</span> {{ $copy->accession->accession_number ?? '—' }}</flux:text>
            <flux:text><span class="font-semibold">Call Number:</span> {{ $copy->accession->call_number ?? '—' }}</flux:text>
        </div>
        <div class="flex flex-col gap-2">
            <flux:text><span class="font-semibold">Copy ID:</span> {{ $copy->copy_id }}</flux:text>
            <flux:text>
                <span class="font-semibold">Status:</span>
                <flux:badge color="{{ $copy->status === 'Available' ? 'green' : 'yellow' }}" size="sm">
                    {{ $copy->status }}
                </flux:badge>
            </flux:text>
            <flux:text><span class="font-semibold">Condition:</span> {{ $copy->condition ?? '—' }}</flux:text>
        </div>
    </div>

    <flux:separator />

    @if($copy->status === 'Available')

    <div class="flex flex-col gap-3">
        <flux:heading size="sm">Issue this copy</flux:heading>

        <div class="flex gap-2">
            <flux:button
                size="sm"
                variant="{{ $borrowerType === 'student' ? 'primary' : 'ghost' }}"
                wire:click="$set('borrowerType', 'student')">
                Student
            </flux:button>
            <flux:button
                size="sm"
                variant="{{ $borrowerType === 'faculty' ? 'primary' : 'ghost' }}"
                wire:click="$set('borrowerType', 'faculty')">
                Faculty
            </flux:button>
        </div>

        <div class="flex gap-2 items-start">
            <div class="flex flex-col gap-1 flex-1 relative">
                <flux:input
                    wire:model.live.debounce.300ms="borrowerSearch"
                    placeholder="{{ $borrowerType === 'student' ? 'Search student by name or ID...' : 'Search faculty by name or ID...' }}"
                    size="sm" />

                @error('borrowerId')
                <flux:text class="text-red-500 text-xs">{{ $message }}</flux:text>
                @enderror

                @if($borrowerId && empty($borrowerResults))
                <flux:text class="text-xs text-green-600 dark:text-green-400">
                    Selected: {{ $borrowerId }}
                </flux:text>
                @endif

                @if(!empty($borrowerResults))
                <div class="absolute z-50 w-full top-full mt-1 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 shadow-lg max-h-60 overflow-y-auto">
                    @foreach($borrowerResults as $b)
                    <button
                        type="button"
                        wire:click="selectBorrower('{{ $b['id'] }}')"
                        class="w-full px-3 py-2 text-left hover:bg-zinc-100 dark:hover:bg-zinc-700 border-b border-zinc-200 dark:border-zinc-700 last:border-b-0">
                        <div class="flex justify-between items-center">
                            <span class="font-medium">{{ $b['full_name'] }}</span>
                            <span class="text-sm text-zinc-500">{{ $b['id'] }}</span>
                        </div>
                    </button>
                    @endforeach
                </div>
                @elseif(strlen($borrowerSearch) > 1 && empty($borrowerResults) && !$borrowerId)
                <flux:callout variant="warning" class="mt-1" size="sm">
                    <flux:callout.text class="text-xs">
                        No {{ $borrowerType }} found matching "{{ $borrowerSearch }}".
                        <a href="{{ $borrowerType === 'student' ? route('users.students-index') : route('users.faculties-index') }}"
                            class="underline font-semibold"
                            target="_blank">
                            Add new {{ $borrowerType }}
                        </a>
                    </flux:callout.text>
                </flux:callout>
                @endif
            </div>

            <div class="flex flex-col gap-1 flex-1">
                <flux:input
                    type="date"
                    wire:model="dueDate"
                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                    size="sm" />
                @error('dueDate')
                <flux:text class="text-red-500 text-xs">{{ $message }}</flux:text>
                @enderror
            </div>

            <flux:button
                size="sm"
                variant="primary"
                color="amber"
                wire:click="borrow"
                wire:loading.attr="disabled"
                wire:target="borrow">
                <span wire:loading.remove wire:target="borrow">Issue</span>
                <span wire:loading wire:target="borrow">Issuing…</span>
            </flux:button>
        </div>
    </div>
    @elseif($copy->status === 'Borrowed')

    @php
    $activeBorrow = $copy->studentBorrows->first() ?? $copy->facultyBorrows->first();
    $borrowerName = $activeBorrow?->student?->full_name
    ?? $activeBorrow?->faculty?->full_name
    ?? 'Unknown';
    $dueDate = $activeBorrow?->due_date?->format('M d, Y') ?? '—';
    $isOverdue = $activeBorrow?->due_date && $activeBorrow->due_date->isPast();
    @endphp

    <div class="flex flex-col gap-3">
        <flux:heading size="sm">Currently issued to</flux:heading>

        <div class="flex items-center justify-between gap-4 p-3 rounded-lg bg-zinc-50 dark:bg-zinc-700">
            <div class="flex flex-col gap-1">
                <flux:text class="font-semibold">{{ $borrowerName }}</flux:text>
                <flux:text class="text-sm">
                    Due: {{ $dueDate }}
                    @if($isOverdue)
                    <flux:badge color="red" size="sm">Overdue</flux:badge>
                    @endif
                </flux:text>
            </div>

            <div class="flex gap-2 items-start">
                <div class="flex flex-col gap-1">
                    <flux:select wire:model="returnCondition" size="sm" placeholder="Condition on return">
                        @foreach($conditions as $condition)
                        <flux:select.option value="{{ $condition }}">{{ $condition }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    @error('returnCondition')
                    <flux:text class="text-red-500 text-xs">{{ $message }}</flux:text>
                    @enderror
                </div>

                <flux:button
                    size="sm"
                    variant="primary"
                    color="green"
                    wire:click="return"
                    wire:loading.attr="disabled"
                    wire:target="return">
                    <span wire:loading.remove wire:target="return">Return</span>
                    <span wire:loading wire:target="return">Processing…</span>
                </flux:button>
            </div>
        </div>
    </div>

    @endif

    @php
    $allBorrows = collect();

    foreach($copy->studentBorrows as $b) {
    $allBorrows->push([
    'name' => $b->student?->full_name ?? '—',
    'type' => 'Student',
    'borrow_ref' => $b->ref_number,
    'return_ref' => $b->return_ref_number,
    'date_borrowed' => $b->date_borrowed,
    'due_date' => $b->due_date,
    'date_returned' => $b->date_returned,
    ]);
    }

    foreach($copy->facultyBorrows as $b) {
    $allBorrows->push([
    'name' => $b->faculty?->full_name ?? '—',
    'type' => 'Faculty',
    'borrow_ref' => $b->ref_number,
    'return_ref' => $b->return_ref_number,
    'date_borrowed' => $b->date_borrowed,
    'due_date' => $b->due_date,
    'date_returned' => $b->date_returned,
    ]);
    }

    $allBorrows = $allBorrows->sortByDesc('date_borrowed');
    @endphp

    @if($allBorrows->isNotEmpty())
    <flux:separator />
    <flux:heading size="sm">Borrow history</flux:heading>
    <flux:table>
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Type</flux:table.column>
            <flux:table.column>Borrow Ref</flux:table.column>
            <flux:table.column>Borrowed</flux:table.column>
            <flux:table.column>Due</flux:table.column>
            <flux:table.column>Return Ref</flux:table.column>
            <flux:table.column>Returned</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach($allBorrows as $row)
            <flux:table.row>
                <flux:table.cell>{{ $row['name'] }}</flux:table.cell>
                <flux:table.cell>
                    <flux:badge color="{{ $row['type'] === 'Student' ? 'blue' : 'purple' }}" size="sm">
                        {{ $row['type'] }}
                    </flux:badge>
                </flux:table.cell>
                <flux:table.cell>
                    <flux:text size="xs" class="font-mono">{{ $row['borrow_ref'] }}</flux:text>
                </flux:table.cell>
                <flux:table.cell>{{ $row['date_borrowed']?->format('M d, Y') ?? '—' }}</flux:table.cell>
                <flux:table.cell>{{ $row['due_date']?->format('M d, Y') ?? '—' }}</flux:table.cell>
                <flux:table.cell>
                    @if($row['return_ref'])
                    <flux:text size="xs" class="font-mono">{{ $row['return_ref'] }}</flux:text>
                    @else
                    <flux:text size="xs" class="text-zinc-400">—</flux:text>
                    @endif
                </flux:table.cell>
                <flux:table.cell>
                    @if($row['date_returned'])
                    <div class="flex flex-col">
                        <flux:text>{{ $row['date_returned']->format('M d, Y') }}</flux:text>
                        @php
                        $dueDate = \Carbon\Carbon::parse($row['due_date']);
                        $returnDate = \Carbon\Carbon::parse($row['date_returned']);
                        $wasOverdue = $returnDate->isAfter($dueDate);
                        @endphp
                        @if($wasOverdue)
                        <flux:badge color="red" size="xs">Was Overdue</flux:badge>
                        @endif
                    </div>
                    @else
                    <flux:badge color="yellow" size="sm">Active</flux:badge>
                    @endif
                </flux:table.cell>
            </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
    @endif

</div>
