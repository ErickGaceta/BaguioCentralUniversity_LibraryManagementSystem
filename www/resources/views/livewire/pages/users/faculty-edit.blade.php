<div class="flex flex-col gap-4 p-4 rounded-lg bg-white dark:bg-zinc-800 border border-solid border-zinc-600" style="width: 100%; max-width: 600px;">
    <div class="flex justify-between items-center">
        <flux:heading size="lg">Edit Faculty</flux:heading>
        <flux:button
            variant="ghost"
            size="sm"
            icon="x-mark"
            wire:click="closeModal" />
    </div>

    <flux:separator />

    <form wire:submit="updateFaculty" class="flex gap-4">
        <div class="flex flex-col gap-2">
            <flux:input
                wire:model="first_name"
                label="First Name"
                placeholder="Enter first name"
                required />

            <flux:input
                wire:model="middle_name"
                label="Middle Name"
                placeholder="Enter middle name (optional)" />

            <flux:input
                wire:model="last_name"
                label="Last Name"
                placeholder="Enter last name"
                required />
        </div>

        <div class="flex flex-col gap-2">
            <flux:select
                wire:model="department_id"
                label="Department"
                required>
                <flux:select.option value="">Select department</flux:select.option>
                @foreach($departments as $dept)
                <flux:select.option value="{{ $dept->department_code }}">{{ $dept->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input
                wire:model="occupation"
                label="Occupation"
                placeholder="Enter occupation (e.g., Professor, Instructor)"
                required />

            @error('first_name') <flux:error>{{ $message }}</flux:error> @enderror
            @error('last_name') <flux:error>{{ $message }}</flux:error> @enderror
            @error('department_id') <flux:error>{{ $message }}</flux:error> @enderror
            @error('occupation') <flux:error>{{ $message }}</flux:error> @enderror

            <div class="flex gap-2 mt-4">
                <flux:button type="submit" variant="primary" class="flex-1">Update Faculty</flux:button>
                <flux:button type="button" variant="ghost" wire:click="closeModal">Cancel</flux:button>
            </div>
        </div>
    </form>
</div>
