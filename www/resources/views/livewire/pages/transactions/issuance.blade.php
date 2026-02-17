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
        <flux:text>A table for the transactions made by the librarian <flux:text size="xs">(Borrow and Return)</flux:text>
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
                        $badgeColor = match($borrow->transaction_name) {
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
                    <flux:table.cell>{{ \Carbon\Carbon::parse($borrow->transaction_date)->format('M d, Y') }}</flux:table.cell>
                    <flux:table.cell>{{ \Carbon\Carbon::parse($borrow->transaction_date)->format('h:i A') }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex flex-col">
                            <flux:text class="font-medium">{{ $borrow->name }}</flux:text>
                            <flux:text size="xs" class="text-zinc-500">{{ $borrow->display_id }}</flux:text>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button
                            icon="archive-box-arrow-down"
                            variant="danger"
                            wire:click="archiveTransaction('{{ $borrow->type }}', {{ $borrow->id }})"
                            wire:confirm="Archive this transaction ({{ $borrow->ref_number }})? It will be moved to transaction archives." />
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
</div>
