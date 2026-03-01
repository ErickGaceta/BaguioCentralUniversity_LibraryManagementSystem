<div class="w-full flex flex-col gap-4 p-3">
    <x-flash />
    <div>
        <flux:heading size="xl">Library Transactions</flux:heading>
        <flux:text>A table for the transactions made by the librarian <flux:text size="xs">(Borrow and Return)
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
                @forelse($borrows as $borrow)
                    <flux:table.row>
                        <flux:table.cell>
                            @php
                                $badgeColor = match ($borrow->transaction_name) {
                                    'Student Borrow' => 'blue',
                                    'Faculty Borrow' => 'purple',
                                    'Student Return' => 'green',
                                    'Faculty Return' => 'amber',
                                    default => 'zinc'
                                };
                            @endphp
                            <flux:badge color="{{ $badgeColor }}" size="sm">
                                {{ $borrow->transaction_name }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ $borrow->ref_number }}</flux:table.cell>
                        <flux:table.cell>{{ \Carbon\Carbon::parse($borrow->transaction_date)->format('M d, Y') }}
                        </flux:table.cell>
                        <flux:table.cell>{{ \Carbon\Carbon::parse($borrow->transaction_date)->format('h:i A') }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-col">
                                <flux:text class="font-medium">{{ $borrow->name }}</flux:text>
                                <flux:text size="xs" class="text-zinc-500">{{ $borrow->display_id }}</flux:text>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:button icon="archive-box-arrow-down" variant="danger"
                                x-on:click="$flux.modal('archive-transaction').show(); $wire.set('archivingTransactionType', '{{ $borrow->type }}'); $wire.set('archivingTransactionId', {{ $borrow->id }})" />

                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" align="center">No Issuance Transactions</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-pagination :paginator="$borrows" />
    </div>
    <x-confirm-modal name="archive-transaction" title="Archive Transaction"
        description="This transaction will be moved to the transaction archives. Continue?" confirm-label="Archive"
        confirm-variant="danger" confirm-action="archiveTransactionConfirmed" />
</div>
