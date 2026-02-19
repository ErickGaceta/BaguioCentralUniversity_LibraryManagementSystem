<div class="flex flex-col gap-4 p-4 rounded-lg bg-white dark:bg-zinc-800 border border-solid border-zinc-600"
    style="width: 100%; max-width: 800px;" x-data="{
        copyCount: $wire.entangle('copies'),
        originalCount: {{ $originalCopies }},
        newCopies: [],

        get addedCount() {
            const diff = (parseInt(this.copyCount) || 0) - this.originalCount;
            return diff > 0 ? diff : 0;
        },

        syncNewCopies() {
            const n = this.addedCount;
            while (this.newCopies.length < n) {
                this.newCopies.push({ accession_number: '', call_number: '' });
            }
            this.newCopies = this.newCopies.slice(0, n);
        },

        async submit() {
            await $wire.updateBook(this.newCopies);
        }
    }" x-effect="syncNewCopies()">
    <div class="flex justify-between items-center">
        <flux:heading size="lg">Edit Book</flux:heading>
        <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="closeModal" />
    </div>

    <flux:separator />

    <form @submit.prevent="submit()" class="flex gap-4">

        {{-- Left column --}}
        <div class="flex flex-col gap-2">
            <flux:input wire:model="title" label="Title" placeholder="Enter book title" required />

            <flux:input wire:model="author" label="Author" placeholder="Enter author name" required />

            <flux:input wire:model="publisher" label="Publisher" placeholder="Enter publisher name" />

            <flux:input wire:model="isbn" label="ISBN" placeholder="Enter ISBN" />

            <flux:input type="date" wire:model="publication_date" label="Publication Date" />
        </div>

        {{-- Right column --}}
        <div class="flex flex-col gap-2 flex-1">

            <flux:select wire:model.live="department_id" label="Department" required>
                <flux:select.option value="">Select department</flux:select.option>
                @foreach($departments as $dept)
                    <flux:select.option value="{{ $dept->department_code }}">{{ $dept->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model="course_id" label="Course (for copies)" placeholder="Select course" required
                :disabled="!$department_id">
                @if($department_id)
                    <flux:select.option value="">Select course</flux:select.option>
                    @foreach($filteredCourses as $course)
                        <flux:select.option value="{{ $course->course_code }}">{{ $course->name }}</flux:select.option>
                    @endforeach
                @endif
            </flux:select>

            <flux:input wire:model="category" label="Type" placeholder="e.g., Research Paper, Academic Book, etc." />

            <flux:input type="number" x-model="copyCount" label="Number of Copies" min="1" required />

            {{-- Only shown when copies are being added --}}
            <div x-show="addedCount > 0" x-cloak class="flex flex-col gap-2 mt-1">

                <flux:separator />

                <div class="flex items-center gap-2">
                    <flux:icon name="document-text" class="size-4 text-zinc-500 dark:text-zinc-400" />
                    <flux:text size="sm" class="font-semibold">New Copy Cataloging</flux:text>
                    <flux:badge size="sm" color="zinc"
                        x-text="'+' + addedCount + ' new ' + (addedCount === 1 ? 'copy' : 'copies')"></flux:badge>
                </div>

                {{-- Column headers --}}
                <div class="grid grid-cols-[4.5rem_1fr_1fr] gap-2">
                    <span></span>
                    <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400 font-medium">
                        Accession No. <span class="text-red-500">*</span>
                    </flux:text>
                    <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400 font-medium">
                        Call No. <span class="text-red-500">*</span>
                    </flux:text>
                </div>

                <template x-for="(copy, index) in newCopies" :key="index">
                    <div class="grid grid-cols-[4.5rem_1fr_1fr] items-center gap-2">
                        <span class="text-right text-xs text-zinc-500 dark:text-zinc-400"
                            x-text="'Copy #' + (originalCount + index + 1)"></span>
                        <flux:input x-model="newCopies[index].accession_number" placeholder="e.g. 2024-00004"
                            size="sm" />
                        <flux:input x-model="newCopies[index].call_number" placeholder="e.g. 005.13 DEL" size="sm" />
                    </div>
                </template>

                @error('copies')
                    <flux:text size="sm" class="text-red-500">{{ $message }}</flux:text>
                @enderror

            </div>

            @error('title') <flux:error>{{ $message }}</flux:error> @enderror
            @error('author') <flux:error>{{ $message }}</flux:error> @enderror
            @error('department_id') <flux:error>{{ $message }}</flux:error> @enderror
            @error('course_id') <flux:error>{{ $message }}</flux:error> @enderror
            @error('copies') <flux:error>{{ $message }}</flux:error> @enderror

            <div class="flex gap-2 mt-4">
                <flux:button type="submit" variant="primary" class="flex-1">Update Book</flux:button>
                <flux:button type="button" variant="ghost" wire:click="closeModal">Cancel</flux:button>
            </div>

        </div>

    </form>
</div>
