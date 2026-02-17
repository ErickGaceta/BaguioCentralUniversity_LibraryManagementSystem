<div>
    <flux:modal name="generate-report" class="w-full max-w-xl">
        <div>
            <div class="space-y-1 mb-6">
                <flux:heading size="lg">Generate Report</flux:heading>
                <flux:subheading>Configure the report type and date range, then click Generate.</flux:subheading>
            </div>

            @if (session('error'))
            <flux:callout variant="danger" class="mb-4" icon="exclamation-circle">
                {{ session('error') }}
            </flux:callout>
            @endif

            <div class="flex flex-col gap-3">

                <flux:field>
                    <flux:label>Report Type
                    </flux:label>
                    <flux:select required wire:model.live="reportType" placeholder="Select a report type…">
                        @foreach ($reportTypes as $value => $label)
                        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="reportType" />
                </flux:field>

                <flux:field>
                    <flux:label>Period</flux:label>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach ($presets as $value => $label)
                        <label
                            class="flex items-center justify-center gap-2 rounded-lg border px-3 py-2 text-sm cursor-pointer transition
                                       {{ $preset === $value
                                            ? 'border-[#860805] bg-[#860805]/10 text-[#860805] font-semibold dark:border-[#c0403c] dark:bg-[#c0403c]/15 dark:text-[#eae0d2]'
                                            : 'border-zinc-200 hover:border-zinc-300 dark:border-zinc-600 dark:hover:border-zinc-500' }}">
                            <input
                                type="radio"
                                wire:model.live="preset"
                                value="{{ $value }}"
                                class="sr-only" />
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                    <flux:error name="preset" />
                </flux:field>

                @if (in_array($preset, ['annual', 'semi_annual', 'quarterly', 'monthly']))
                @php
                $cols = match($preset) {
                'monthly' => 2,
                'quarterly' => 3,
                'semi_annual' => 3,
                default => 1,
                };
                @endphp
                <div class="grid gap-4 {{ 'grid-cols-' . $cols }}">

                    <flux:field>
                        <flux:label>Year</flux:label>
                        <flux:select wire:model.live="year">
                            @foreach ($years as $y)
                            <flux:select.option value="{{ $y }}">{{ $y }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    @if ($preset === 'semi_annual')
                    <flux:field class="col-span-2">
                        <flux:label>Half</flux:label>
                        <flux:select wire:model.live="quarter">
                            <flux:select.option value="1">First Half (Jan – Jun)</flux:select.option>
                            <flux:select.option value="2">Second Half (Jul – Dec)</flux:select.option>
                        </flux:select>
                    </flux:field>
                    @endif

                    @if ($preset === 'quarterly')
                    <flux:field class="col-span-2">
                        <flux:label>Quarter</flux:label>
                        <flux:select wire:model.live="quarter">
                            <flux:select.option value="1">Q1 – Jan to Mar</flux:select.option>
                            <flux:select.option value="2">Q2 – Apr to Jun</flux:select.option>
                            <flux:select.option value="3">Q3 – Jul to Sep</flux:select.option>
                            <flux:select.option value="4">Q4 – Oct to Dec</flux:select.option>
                        </flux:select>
                    </flux:field>
                    @endif

                    @if ($preset === 'monthly')
                    <flux:field>
                        <flux:label>Month</flux:label>
                        <flux:select wire:model.live="month">
                            @foreach ($months as $num => $name)
                            <flux:select.option value="{{ $num }}">{{ $name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                    @endif
                    </>
                    @endif

                    @if ($preset === 'custom')
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>From
                            </flux:label>
                            <flux:input required type="date" wire:model.live="customFrom" />
                            <flux:error name="customFrom" />
                        </flux:field>
                        <flux:field>
                            <flux:label>To
                            </flux:label>
                            <flux:input required type="date" wire:model.live="customTo" />
                            <flux:error name="customTo" />
                        </flux:field>
                    </div>
                    @endif

                    @if ($resolvedFrom && $resolvedTo)
                    <flux:callout variant="success" icon="calendar-days">
                        <flux:callout.heading class="text-zinc-500 dark:text-zinc-400">Coverage</flux:callout.heading>
                        <flux:text class="font-medium text-zinc-800 dark:text-zinc-100">
                            {{ \Carbon\Carbon::parse($resolvedFrom)->format('M d, Y') }}
                            &ndash;
                            {{ \Carbon\Carbon::parse($resolvedTo)->format('M d, Y') }}
                        </flux:text>
                    </flux:callout>
                    @endif

                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>

                    <flux:button
                        variant="primary"
                        icon="document-arrow-down"
                        wire:click="generate"
                        style="background-color: #860805;"
                        class="hover:opacity-90 transition text-white">
                            Generate Report
                    </flux:button>
                </div>

            </div>
    </flux:modal>
</div>
