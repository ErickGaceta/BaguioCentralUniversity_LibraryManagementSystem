<div>
    @if($show)
        <div class="fixed inset-0 flex items-center justify-center" style="z-index: 1100; background: rgba(0,0,0,0.5); width: 100%; max-width: 800px; opacity: 1;"
            x-data="{
                copies: Array.from({ length: {{ $copies }} }, () => ({ accession_number: '', call_number: '' })),

                async save() {
                    await $wire.save(this.copies);
                }
            }">
            <div class="flex flex-col gap-4 p-4 rounded-lg bg-white dark:bg-zinc-800 border border-solid border-zinc-600 w-full max-w-lg"
                style="max-height: 80vh; overflow-y: auto;">

                {{-- Header --}}
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <flux:icon name="document-text" class="size-5 text-zinc-500 dark:text-zinc-400" />
                        <flux:heading size="lg">Catalog Numbers</flux:heading>
                        <flux:badge color="zinc">{{ $copies }} {{ Str::plural('copy', $copies) }}</flux:badge>
                    </div>
                    <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="close" />
                </div>

                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
                    Enter the accession and call number for each copy of
                    <strong class="text-zinc-700 dark:text-zinc-200">{{ $bookTitle }}</strong>.
                </flux:text>

                <flux:separator />

                {{-- Column headers --}}
                <div class="grid gap-2 px-1" style="grid-template-columns: 4.5rem 1fr 1fr;">
                    <span></span>
                    <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400 font-medium">
                        Accession No. <span class="text-red-500">*</span>
                    </flux:text>
                    <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400 font-medium">
                        Call No. <span class="text-red-500">*</span>
                    </flux:text>
                </div>

                {{-- Copy rows — plain inputs so Alpine x-model works correctly --}}
                <div class="flex flex-col gap-2 px-1">
                    <template x-for="(copy, index) in copies" :key="index">
                        <div class="grid items-center gap-2" style="grid-template-columns: 4.5rem 1fr 1fr;">
                            <span class="text-right text-xs text-zinc-500 dark:text-zinc-400"
                                x-text="'Copy #' + (index + 1)"></span>
                            <input type="text" x-model="copies[index].accession_number" placeholder="e.g. 2024-00001"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-1.5 text-sm text-zinc-800 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-400" />
                            <input type="text" x-model="copies[index].call_number" placeholder="e.g. 005.13 DEL"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-1.5 text-sm text-zinc-800 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-400" />
                        </div>
                    </template>
                </div>

                @error('catalog')
                    <flux:text size="sm" class="text-red-500 px-1">{{ $message }}</flux:text>
                @enderror

                <flux:separator />

                {{-- Actions --}}
                <div class="flex justify-between items-center px-1">
                    <flux:button variant="ghost" wire:click="close">
                        ← Back
                    </flux:button>
                    <flux:button variant="primary" x-on:click="save()">
                        Save Book
                    </flux:button>
                </div>

            </div>
        </div>
    @endif
</div>
