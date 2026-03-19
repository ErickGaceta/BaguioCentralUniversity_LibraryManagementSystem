<div class="w-full flex flex-col gap-4 p-3">
    <x-flash />

    <div>
        <flux:heading size="xl">Library Archive</flux:heading>
        <flux:text>Archived books from the library</flux:text>
    </div>

    <div class="flex flex-col gap-4">

        {{-- Bulk-action toolbar: only visible when rows are selected --}}
        @if(!empty($selectedIds))
            <div
                class="flex items-center gap-3 px-3 py-2 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                <flux:text class="text-sm font-medium text-red-700 dark:text-red-300">
                    {{ count($selectedIds) }} book(s) selected
                </flux:text>
                <flux:spacer />
                <flux:button variant="danger" size="sm" icon="trash" x-on:click="$flux.modal('delete-books-bulk').show()">
                    Delete Selected
                </flux:button>
            </div>
        @endif

        <flux:table>
            <flux:table.columns>
                <flux:table.column>
                    {{-- Select-all checkbox --}}
                    <input type="checkbox" wire:model.live="selectAll"
                        class="rounded border-zinc-300 dark:border-zinc-600" />
                </flux:table.column>
                <flux:table.column>Title</flux:table.column>
                <flux:table.column>Author</flux:table.column>
                <flux:table.column>ISBN</flux:table.column>
                <flux:table.column>Publisher</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Copies</flux:table.column>
                <flux:table.column>Archived Date</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($archivedBooks as $book)
                    <flux:table.row>
                        <flux:table.cell>
                            <input type="checkbox" value="{{ $book->id }}" wire:model.live="selectedIds"
                                class="rounded border-zinc-300 dark:border-zinc-600" />
                        </flux:table.cell>
                        <flux:table.cell>{{ $book->title }}</flux:table.cell>
                        <flux:table.cell>{{ $book->author }}</flux:table.cell>
                        <flux:table.cell>{{ $book->isbn }}</flux:table.cell>
                        <flux:table.cell>{{ $book->publisher }}</flux:table.cell>
                        <flux:table.cell>{{ $book->department->name ?? 'N/A' }}</flux:table.cell>
                        <flux:table.cell>{{ $book->copies }}</flux:table.cell>
                        <flux:table.cell>{{ $book->created_at->format('M d, Y') }}</flux:table.cell>
                        <flux:table.cell align="end">
                            <div class="flex gap-1 justify-end">
                                {{-- Restore --}}
                                <flux:button icon="arrow-uturn-left" size="sm"
                                    x-on:click="$flux.modal('restore-book').show(); $wire.set('restoringId', {{ $book->id }})" />

                                {{-- Single permanent delete --}}
                                <flux:button icon="trash" size="sm" variant="danger"
                                    x-on:click="$wire.set('deletingId', {{ $book->id }}); $flux.modal('delete-book').show()" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="9" class="text-center">
                            No Archived Books Found
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        {{ $archivedBooks->links() }}
    </div>

    {{-- Restore modal --}}
    <x-restore-modal name="restore-book" title="Restore Book"
        description="Are you sure you want to restore this book? It will be moved back to the active books list."
        confirm-action="restoreConfirmed" />

    {{-- Single delete confirmation modal --}}
    <flux:modal name="delete-book" class="max-w-sm">
        <div class="flex flex-col gap-4 p-1">
            <div>
                <flux:heading size="lg">Permanently Delete Book</flux:heading>
                <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    This action is <strong>irreversible</strong>. The book will be removed from the archive and cannot
                    be recovered.
                </flux:text>
            </div>
            <div class="flex justify-end gap-2">
                <flux:button x-on:click="$flux.modal('delete-book').close()">
                    Cancel
                </flux:button>
                <flux:button variant="danger" wire:click="deleteConfirmed"
                    x-on:click="$flux.modal('delete-book').close()">
                    Delete Permanently
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Bulk delete confirmation modal --}}
    <flux:modal name="delete-books-bulk" class="max-w-sm">
        <div class="flex flex-col gap-4 p-1">
            <div>
                <flux:heading size="lg">Permanently Delete Selected Books</flux:heading>
                <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    You are about to permanently delete <strong>{{ count($selectedIds) }} book(s)</strong>.
                    This action is <strong>irreversible</strong> and cannot be undone.
                </flux:text>
            </div>
            <div class="flex justify-end gap-2">
                <flux:button x-on:click="$flux.modal('delete-books-bulk').close()">
                    Cancel
                </flux:button>
                <flux:button variant="danger" wire:click="deleteSelectedConfirmed"
                    x-on:click="$flux.modal('delete-books-bulk').close()">
                    Delete {{ count($selectedIds) }} Book(s)
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
