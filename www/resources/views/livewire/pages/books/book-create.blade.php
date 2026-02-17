<div class="flex flex-col gap-4 p-4 rounded-lg bg-white dark:bg-zinc-800 border border-solid border-zinc-600" style="width: 100%; max-width: 800px; opacity: 1;">
    <div class="flex justify-between items-center">
        <flux:heading size="lg">Add Book</flux:heading>
        <flux:button
            variant="ghost"
            size="sm"
            icon="x-mark"
            wire:click="closeModal" />
    </div>

    <flux:separator />

    <form wire:submit="saveBook" class="flex gap-4">
        <div class="flex flex-col gap-2">
            <flux:input
                wire:model="title"
                label="Title"
                placeholder="Enter book title"
                required />

            <flux:input
                wire:model="author"
                label="Author"
                placeholder="Enter author name"
                required />

            <flux:input
                wire:model="publisher"
                label="Publisher"
                placeholder="Enter publisher name" />

            <flux:input
                wire:model="isbn"
                label="ISBN"
                placeholder="Enter ISBN" />

            <flux:input
                type="date"
                wire:model="publication_date"
                label="Publication Date" />
        </div>

        <div class="flex flex-col gap-2">
            <flux:select
                wire:model.live="department_id"
                label="Department"
                required>
                <flux:select.option value="" selected>Select department</flux:select.option>
                @foreach($departments as $dept)
                <flux:select.option value="{{ $dept->department_code }}">{{ $dept->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select
                wire:model="course_id"
                label="Course (for copies)"
                placeholder="Select course"
                required
                :disabled="!$department_id">
                @if($department_id)
                @foreach($filteredCourses as $course)
                <flux:select.option value="{{ $course->course_code }}">{{ $course->name }}</flux:select.option>
                @endforeach
                @endif
            </flux:select>

            <flux:input
                wire:model="category"
                label="Type"
                placeholder="e.g., Research Paper, Academic Book,. etc." />

            <flux:input
                type="number"
                wire:model="copies"
                label="Number of Copies"
                min="1"
                value="1"
                required />

            @error('title') <flux:error>{{ $message }}</flux:error> @enderror

            <flux:button type="submit" variant="primary" class="mt-4">Save Book</flux:button>
        </div>
    </form>
</div>
