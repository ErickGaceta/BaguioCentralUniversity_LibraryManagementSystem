<div class="flex flex-col gap-4 p-4">

    <div class="flex flex-col">
        <div>
            <flux:heading size="xl">Reports</flux:heading>
            <flux:subheading>Generate and browse library reports by type and date range.</flux:subheading>
        </div>
    </div>

    <div style="position: absolute; bottom: 5px; right: 10px;">
        @if(session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                <flux:card class="flex gap-2 align-center items-center justify-center">
                    <flux:icon.check-circle class="text-green-500" />
                    <flux:separator vertical />
                    <div class="flex flex-col">
                        <flux:heading>Success!</flux:heading>
                        <flux:text>{{ session('success') }}</flux:text>
                    </div>
                </flux:card>
            </div>
        @endif
    </div>

    <div class="flex gap-3 sm:flex-row sm:items-center">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search reports…" icon="magnifying-glass"
                clearable />
        </div>
        <div class="flex gap-2">
            <flux:modal.trigger name="generate-report">
                <flux:button variant="primary" icon="clipboard-document-list" color="amber" class="hover:opacity-90 transition shrink-0"
                    x-on:click="$flux.modal('generate-report').show()">
                    Generate Report
                </flux:button>
            </flux:modal.trigger>

            <flux:select wire:model.live="typeFilter" placeholder="All types" class="sm:w-56">
                @foreach ($reportTypes as $value => $label)
                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Title</flux:table.column>
            <flux:table.column>Type</flux:table.column>
            <flux:table.column>Period</flux:table.column>
            <flux:table.column>Coverage</flux:table.column>
            <flux:table.column>Records</flux:table.column>
            <flux:table.column>Generated On</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($reports as $report)
                <flux:table.row wire:key="report-{{ $report->id }}">

                    <flux:table.cell class="font-medium max-w-60">
                        <span class="truncate block" title="{{ $report->title }}">{{ $report->title }}</span>
                    </flux:table.cell>

                    <flux:table.cell>
                        @php
    $badgeColor = match ($report->report_type) {
        'issuance' => 'blue',
        'book_added' => 'green',
        'book_archived' => 'yellow',
        'fines_student' => 'orange',
        'fines_faculty' => 'purple',
        'fines_both' => 'red',
        default => 'zinc',
    };
                        @endphp
                        <flux:badge color="{{ $badgeColor }}" size="sm">
                            {{ $report->getTypeLabel() }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ $report->getPresetLabel() }}
                    </flux:table.cell>

                    <flux:table.cell class="text-sm whitespace-nowrap">
                        {{ $report->date_from->format('M d, Y') }}
                        <span class="text-zinc-400 mx-1">–</span>
                        {{ $report->date_to->format('M d, Y') }}
                    </flux:table.cell>

                    <flux:table.cell>
                        <span class="font-tabular-nums">{{ number_format($report->total_records) }}</span>
                    </flux:table.cell>

                    <flux:table.cell class="text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                        {{ $report->created_at->format('M d, Y h:i A') }}
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="flex items-center gap-1 justify-end">

                            <a href="{{ route('reports.pdf', $report) }}" target="_blank">
                                <flux:button size="sm" variant="ghost" icon="eye" title="View / print PDF" />
                            </a>

                            <flux:button size="sm" variant="ghost" icon="trash" title="Delete report"
                                x-on:click="$flux.modal('delete-confirm').show(); $wire.set('deletingId', {{ $report->id }})" />

                        </div>
                    </flux:table.cell>
                </flux:table.row>
                <flux:modal name="delete-confirm" class="max-w-lg">
                    <flux:heading>Delete Report</flux:heading>
                    <flux:text>
                        This will permanently delete this report. This action cannot be undone. Continue?
                    </flux:text>
                    <div class="flex gap-2 mt-4">
                        <flux:button variant="danger" wire:click="deleteConfirmed"
                            x-on:click="$flux.modal('delete-confirm').close()">
                            Delete
                        </flux:button>
                        <flux:button variant="ghost" x-on:click="$flux.modal('delete-confirm').close()">
                            Cancel
                        </flux:button>
                    </div>
                </flux:modal>

            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7">
                        <div class="py-16 flex flex-col items-center gap-3 text-center text-zinc-500 dark:text-zinc-400">
                            <flux:icon.document-magnifying-glass class="size-10 opacity-40" />
                            <p class="font-medium text-zinc-700 dark:text-zinc-300">No reports found</p>
                            @if ($search || $typeFilter)
                                <p class="text-sm">Try clearing your filters.</p>
                                <flux:button size="sm" variant="ghost" wire:click="$set('search', ''); $set('typeFilter', '')">
                                    Clear filters
                                </flux:button>
                            @else
                                <p class="text-sm">Generate your first report using the button above.</p>
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <x-pagination :paginator="$reports" />

    <livewire:pages.reports.generate />

</div>
