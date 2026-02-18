<div class="flex flex-col gap-6 p-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">Reports</flux:heading>
            <flux:subheading>Generate and browse library reports by type and date range.</flux:subheading>
        </div>

        <flux:modal.trigger name="generate-report">
            <flux:button
                variant="primary"
                icon="clipboard-document-list"
                color="amber"
                class="hover:opacity-90 transition shrink-0">
                Generate Report
            </flux:button>
        </flux:modal.trigger>
    </div>

    @if (session('success'))
    <flux:callout variant="success" icon="check-circle" dismissible>
        {{ session('success') }}
    </flux:callout>
    @endif

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="flex-1">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search reports…"
                icon="magnifying-glass"
                clearable />
        </div>

        <flux:select
            wire:model.live="typeFilter"
            placeholder="All types"
            class="sm:w-56">
            @foreach ($reportTypes as $value => $label)
            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
            @endforeach
        </flux:select>
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
                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="eye"
                                title="View / print PDF" />
                        </a>

                        <flux:button
                            size="sm"
                            variant="ghost"
                            icon="trash"
                            title="Delete report"
                            wire:click="delete({{ $report->id }})"
                            wire:confirm="Delete this report? This cannot be undone." />

                    </div>
                </flux:table.cell>
            </flux:table.row>

            @empty
            <flux:table.row>
                <flux:table.cell colspan="7">
                    <div class="py-16 flex flex-col items-center gap-3 text-center text-zinc-500 dark:text-zinc-400">
                        <flux:icon.document-magnifying-glass class="size-10 opacity-40" />
                        <p class="font-medium text-zinc-700 dark:text-zinc-300">No reports found</p>
                        @if ($search || $typeFilter)
                        <p class="text-sm">Try clearing your filters.</p>
                        <flux:button
                            size="sm"
                            variant="ghost"
                            wire:click="$set('search', ''); $set('typeFilter', '')">
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
