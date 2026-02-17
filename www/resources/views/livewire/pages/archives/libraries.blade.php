<div class="w-full flex flex-col gap-4 p-3">
    <div style="position: absolute; bottom: 5px; right: 10px;">
        @if(session()->has('message'))
        <div x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 5000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <flux:card class="flex gap-2 align-center items-center justify-center">
                <flux:icon.check-circle class="text-green-500" />
                <flux:separator vertical />
                <div class="flex flex-col">
                    <flux:heading>Success!</flux:heading>
                    <flux:text>{{ session('message') }}</flux:text>
                </div>
            </flux:card>
        </div>
        @endif
    </div>

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
                        <flux:button icon="arrow-uturn-left" size="sm" wire:click="openRestoreModal({{ $book->id }})" />
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

    @if($showRestoreModal)
    <flux:modal wire:model="showRestoreModal">
        <flux:heading>Restore Book</flux:heading>
        <flux:text>
            Are you sure you want to restore this book? It will be moved back to the active books list.
        </flux:text>
        <div>
            <flux:button wire:click="closeRestoreModal" variant="ghost">Cancel</flux:button>
            <flux:button wire:click="restoreBook" variant="primary">Restore</flux:button>
        </div>
    </flux:modal>
    @endif
</div>
