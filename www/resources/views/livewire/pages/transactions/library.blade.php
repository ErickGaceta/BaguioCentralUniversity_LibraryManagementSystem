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
        <flux:heading size="xl">Library Transactions</flux:heading>
        <flux:text>A table for the transactions made by the librarian <flux:text size="xs">(Add, Edit, and Archive Book)</flux:text>
        </flux:text>
    </div>

    <div>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Transaction Name</flux:table.column>
                <flux:table.column>Reference Number</flux:table.column>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column>Time</flux:table.column>
                <flux:table.column>Made By</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($libTransactions as $lt)
                <flux:table.row>
                    <flux:table.cell>{{ $lt->transaction_name }}</flux:table.cell>
                    <flux:table.cell>{{ $lt->ref_number }}</flux:table.cell>
                    <flux:table.cell>{{ $lt->created_at->format('M d, Y') }}</flux:table.cell>
                    <flux:table.cell>{{ $lt->created_at->format('h:i A') }}</flux:table.cell>
                    <flux:table.cell>Admin</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button
                            icon="archive-box-arrow-down"
                            variant="danger"
                            wire:click="archiveLibraryTransaction({{ $lt->id }})"
                            wire:confirm="Archive this transaction ({{ $lt->ref_number }})? It will be moved to transaction archives." />
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row col-span="6">
                    <flux:table.cell align="center">No Recent Transactions</flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-pagination :paginator="$libTransactions" />
    </div>
</div>
