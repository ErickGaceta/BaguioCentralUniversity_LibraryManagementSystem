<div class="w-full flex flex-col gap-4 p-3">
    <x-flash />
    <div>
        <flux:heading size="xl">Library Transactions</flux:heading>
        <flux:text>A table for the transactions made by the librarian <flux:text size="xs">(Add, Edit, and Archive Book)
            </flux:text>
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
                            <flux:button icon="archive-box-arrow-down" variant="danger"
                                x-on:click="$flux.modal('archive-library-transaction').show(); $wire.set('archivingLibraryTransactionId', {{ $lt->id }})" />
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
    <x-confirm-modal name="archive-library-transaction" title="Archive Transaction"
        description="This transaction will be moved to the transaction archives. Continue?" confirm-label="Archive"
        confirm-variant="danger" confirm-action="archiveLibraryTransactionConfirmed" />
</div>
