<div class="w-full flex flex-col gap-4 p-3">
    <x-flash />
    <div>
        <flux:heading size="xl">Book Browser</flux:heading>
        <flux:text>List of all books</flux:text>
    </div>

    <div class="flex flex-col gap-4">
        <div class="flex gap-4">
            <flux:input icon="magnifying-glass" type="search" wire:model.live="search" placeholder="Search books..." />

            <flux:button wire:click="$toggle('showCreateModal')" icon="folder-plus" align="end" variant="primary" color="amber">Add Book</flux:button>
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
                                <flux:button icon="eye" wire:click="openEditModal({{ $book->id }})" />
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

    @if($showCreateModal)
    <div class="flex gap-4" style="width: 25vw; z-index: 1000; position: absolute; top: 20%; left: 40%;">
        <livewire:pages.books.book-create />
    </div>
    @endif

    @if($showEditModal && $editingBookId)
    <div class="flex gap-4" style="width: 25vw; z-index: 1000; position: absolute; top: 20%; left: 40%;">
        <livewire:pages.books.book-edit
            :book-id="$editingBookId"
            wire:key="book-edit-{{ $editingBookId }}" />
    </div>
    @endif
</div>
