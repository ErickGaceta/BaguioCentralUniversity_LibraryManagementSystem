<div>
    @if($show)
        @php
        $existingCount = count($existingCopies);
        $existingJson = json_encode($existingCopies);
        @endphp
        <div
            class="fixed inset-0 flex items-center justify-center"
            style="z-index: 1100; background: rgba(0,0,0,0.5);"
            x-data="{
                rows: [
                    ...{{ $existingJson }}.map(c => ({
                        copy_id:          c.copy_id,
                        label:            c.label,
                        accession_number: '',
                        call_number:      '',
                        isExisting:       true,
                    })),
                    ...Array.from({ length: {{ $newCopiesCount }} }, (_, i) => ({
                        copy_id:          null,
                        label:            'New Copy #' + ({{ $existingCount }} + i + 1),
                        accession_number: '',
                        call_number:      '',
                        isExisting:       false,
                    })),
                ],

                async save() {
                    const existingCatalogData = this.rows
                        .filter(r => r.isExisting)
                        .map(r => ({ copy_id: r.copy_id, label: r.label, accession_number: r.accession_number, call_number: r.call_number }));

                    const newCopyData = this.rows
                        .filter(r => !r.isExisting)
                        .map(r => ({ accession_number: r.accession_number, call_number: r.call_number }));

                    await $wire.save(existingCatalogData, newCopyData);
                }
            }"
        >
            <div
                class="flex flex-col gap-4 p-4 rounded-lg bg-white dark:bg-zinc-800 border border-solid border-zinc-600 w-full max-w-lg"
                style="max-height: 80vh; overflow-y: auto; max-width: 600px;"
            >
                {{-- Header --}}
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <flux:icon name="document-text" class="size-5 text-zinc-500 dark:text-zinc-400" />
                        <flux:heading size="lg">Catalog Numbers</flux:heading>
                        <flux:badge color="zinc">
                            {{ $existingCount + $newCopiesCount }} {{ Str::plural('copy', $existingCount + $newCopiesCount) }}
                        </flux:badge>
                    </div>
                    <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="close" />
                </div>

                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
                    Enter the accession and call number for each copy of
                    <strong class="text-zinc-700 dark:text-zinc-200">{{ $bookTitle }}</strong>.
                </flux:text>

                <flux:separator />

                {{-- Section: existing copies missing catalog data --}}
                @if($existingCount > 0)
                    <flux:text size="xs" class="font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">
                        Uncataloged Existing {{ Str::plural('Copy', $existingCount) }}
                    </flux:text>
                @endif

                {{-- Column headers --}}
                <div class="grid gap-2 px-1" style="grid-template-columns: 1fr 1fr 1fr;">
                    <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400 font-medium">Copy</flux:text>
                    <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400 font-medium">
                        Accession No. <span class="text-red-500">*</span>
                    </flux:text>
                    <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400 font-medium">
                        Call No. <span class="text-red-500">*</span>
                    </flux:text>
                </div>

                {{-- Rows --}}
                <div class="flex flex-col gap-2 px-1">
                    <template x-for="(row, index) in rows" :key="index">
                        <div>
                            {{-- Section divider between existing and new --}}
                            <template x-if="index === {{ $existingCount }} && {{ $existingCount }} > 0 && {{ $newCopiesCount }} > 0">
                                <div class="flex items-center gap-2 py-1 mb-1">
                                    <div class="flex-1 border-t border-zinc-200 dark:border-zinc-600"></div>
                                    <flux:text size="xs" class="font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide whitespace-nowrap">
                                        New {{ $newCopiesCount === 1 ? 'Copy' : 'Copies' }}
                                    </flux:text>
                                    <div class="flex-1 border-t border-zinc-200 dark:border-zinc-600"></div>
                                </div>
                            </template>

                            <div class="grid items-center gap-2" style="grid-template-columns: 1fr 1fr 1fr;">
                                <span
                                    class="text-xs text-zinc-600 dark:text-zinc-400 truncate"
                                    x-text="row.label"
                                ></span>
                                <input
                                    type="text"
                                    x-model="rows[index].accession_number"
                                    placeholder="e.g. 2024-00001"
                                    class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-1.5 text-sm text-zinc-800 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-400"
                                />
                                <input
                                    type="text"
                                    x-model="rows[index].call_number"
                                    placeholder="e.g. 005.13 DEL"
                                    class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-1.5 text-sm text-zinc-800 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-400"
                                />
                            </div>
                        </div>
                    </template>
                </div>

                @error('catalog')
                    <flux:text size="sm" class="text-red-500 px-1">{{ $message }}</flux:text>
                @enderror

                <flux:separator />

                {{-- Actions --}}
                <div class="flex justify-between items-center px-1">
                    <flux:button variant="ghost" wire:click="close">← Back</flux:button>
                    <flux:button variant="primary" x-on:click="save()">Save Book</flux:button>
                </div>

            </div>
        </div>
    @endif
</div>
