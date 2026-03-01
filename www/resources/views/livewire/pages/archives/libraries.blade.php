<div class="w-full flex flex-col gap-4 p-3">
    <x-flash />

    <div>
        <flux:heading size="xl">Library Archive</flux:heading>
        <flux:text>Archived books from the library</flux:text>
    </div>

    <div class="flex flex-col gap-4">
        <flux:table>
            <flux:table.columns>
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
                        <flux:table.cell>{{ $book->title }}</flux:table.cell>
                        <flux:table.cell>{{ $book->author }}</flux:table.cell>
                        <flux:table.cell>{{ $book->isbn }}</flux:table.cell>
                        <flux:table.cell>{{ $book->publisher }}</flux:table.cell>
                        <flux:table.cell>{{ $book->department->name ?? 'N/A' }}</flux:table.cell>
                        <flux:table.cell>{{ $book->copies }}</flux:table.cell>
                        <flux:table.cell>{{ $book->created_at->format('M d, Y') }}</flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:button icon="arrow-uturn-left" size="sm"
                                x-on:click="$flux.modal('restore-book').show(); $wire.set('restoringId', {{ $book->id }})" />
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center">
                        No Archived Books Found
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        {{ $archivedBooks->links() }}
    </div>

    <x-restore-modal name="restore-book" title="Restore Book"
        description="Are you sure you want to restore this book? It will be moved back to the active books list."
        confirm-action="restoreConfirmed" />
</div>
