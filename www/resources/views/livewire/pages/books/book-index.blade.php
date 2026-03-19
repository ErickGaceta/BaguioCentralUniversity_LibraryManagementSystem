<div class="w-full flex flex-col gap-4 p-3">
    <x-flash />
    <div>
        <flux:heading size="xl">Book Browser</flux:heading>
        <flux:text>List of all books</flux:text>
    </div>

    <div class="flex flex-col gap-4">
        <div class="flex gap-4">
            <flux:input icon="magnifying-glass" type="search" wire:model.live="search" placeholder="Search books..." />

            <flux:modal.trigger name="create-book">
                <flux:button variant="primary" icon="folder-plus" color="amber" class="hover:opacity-90 transition shrink-0"
                    x-on:click="$flux.modal('create-book').show()">
                    Add Book
                </flux:button>
            </flux:modal.trigger>
            <flux:dropdown>
                <flux:button icon:trailing="chevron-down">
                    {{ $department ? ($departments->firstWhere('department_code', $department)?->name ?? 'Department') : 'All Departments' }}
                </flux:button>

                <flux:menu>
                    <flux:menu.item
                        wire:click="$set('department', '')">
                        All Departments
                    </flux:menu.item>

                    <flux:menu.separator />

                    @foreach($departments as $dept)
                    <flux:menu.item
                        wire:click="$set('department', '{{ $dept->department_code }}')">
                        {{ $dept->name }}
                    </flux:menu.item>
                    @endforeach
                </flux:menu>
            </flux:dropdown>
        </div>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Title</flux:table.column>
                <flux:table.column>Author</flux:table.column>
                <flux:table.column>ISBN</flux:table.column>
                <flux:table.column>Publication Date</flux:table.column>
                <flux:table.column>Copies</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($books as $book)
                    <flux:table.row>
                        <flux:table.cell>{{ $book->title }}</flux:table.cell>
                        <flux:table.cell>{{ $book->author }}</flux:table.cell>
                        <flux:table.cell>{{ $book->isbn }}</flux:table.cell>
                        <flux:table.cell>{{ $book->publication_date }}</flux:table.cell>
                        <flux:table.cell>{{ $book->copies }}</flux:table.cell>
                        <flux:table.cell align="end">
                            <div class="flex gap-2 justify-end">
                                <flux:modal.trigger name="edit-book-{{ $book->id }}">
                                    <flux:button icon="eye" x-on:click="$flux.modal('edit-book-{{ $book->id }}').show()" />
                                </flux:modal.trigger>
                                <flux:button icon="archive-box-arrow-down" variant="danger"
                                    x-on:click="$flux.modal('archive-book').show(); $wire.set('archivingId', {{ $book->id }})" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center">
                        No Books Found
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-pagination :paginator="$books" />
    </div>
    <x-confirm-modal name="archive-book" title="Archive Book"
        description="This will move the book to the archives. Continue?" confirm-label="Archive" confirm-variant="danger"
        confirm-action="archiveConfirmed" />

    <flux:modal name="create-book" class="flex flex-col gap-4 p-4 rounded-lg bg-white dark:bg-zinc-800 border border-solid border-zinc-600">
            <livewire:pages.books.book-create />
    </flux:modal>

    <flux:modal name="edit-book-{{ $book->id }}"
        class="flex flex-col gap-4 p-4 rounded-lg bg-white dark:bg-zinc-800 border border-solid border-zinc-600">
            <livewire:pages.books.book-edit :book-id="$book->id" wire:key="book-edit-{{ $book->id }}" />
    </flux:modal>
</div>
