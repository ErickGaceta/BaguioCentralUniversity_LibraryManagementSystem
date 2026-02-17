<div class="flex flex-col gap-4 p-4 rounded-lg bg-white dark:bg-zinc-800 border border-solid border-zinc-600" style="width: 100%; max-width: 800px; opacity: 1;">
    <div class="flex justify-between items-center">
        <flux:heading size="lg">Add Student</flux:heading>
        <flux:button
            variant="ghost"
            size="sm"
            icon="x-mark"
            wire:click="closeModal" />
    </div>

    <flux:separator />

    <form wire:submit="saveStudent" class="flex gap-4">
        <div class="flex flex-col gap-2">
            <flux:input
                wire:model="student_id"
                label="Student ID"
                placeholder="Enter student ID (e.g., 2024-12345)"
                required />

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
                label="Course"
                placeholder="Select course"
                required
                :disabled="!$department_id">
                @if($department_id)
                <flux:select.option value="" selected>Select course</flux:select.option>
                @foreach($filteredCourses as $course)
                <flux:select.option value="{{ $course->course_code }}">{{ $course->name }}</flux:select.option>
                @endforeach
                @endif
            </flux:select>

            <flux:select
                wire:model="year_level"
                label="Year Level"
                required>
                <flux:select.option value="1">1st Year</flux:select.option>
                <flux:select.option value="2">2nd Year</flux:select.option>
                <flux:select.option value="3">3rd Year</flux:select.option>
                <flux:select.option value="4">4th Year</flux:select.option>
                <flux:select.option value="5">5th Year</flux:select.option>
            </flux:select>

            @error('student_id') <flux:error>{{ $message }}</flux:error> @enderror
            @error('first_name') <flux:error>{{ $message }}</flux:error> @enderror
            @error('last_name') <flux:error>{{ $message }}</flux:error> @enderror
            @error('department_id') <flux:error>{{ $message }}</flux:error> @enderror
            @error('course_id') <flux:error>{{ $message }}</flux:error> @enderror
            @error('year_level') <flux:error>{{ $message }}</flux:error> @enderror

            <flux:button type="submit" variant="primary" class="mt-4">Save Student</flux:button>
        </div>
    </form>
</div>
