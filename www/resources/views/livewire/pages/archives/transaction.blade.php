<div class="w-full flex flex-col gap-4 p-3">
    <x-flash />
    <div>
        <flux:heading size="xl">Transaction Archive</flux:heading>
        <flux:text>Archived transactions from the library system</flux:text>
    </div>

    <div class="flex flex-col gap-4">

        {{-- Type filter buttons --}}
        <div class="flex gap-2 flex-wrap">
            <flux:button wire:click="$set('transactionType', 'all')"
                :variant="$transactionType === 'all' ? 'primary' : 'ghost'">All</flux:button>
            <flux:button wire:click="$set('transactionType', 'student_fines')"
                :variant="$transactionType === 'student_fines' ? 'primary' : 'ghost'">Student Fines</flux:button>
            <flux:button wire:click="$set('transactionType', 'faculty_fines')"
                :variant="$transactionType === 'faculty_fines' ? 'primary' : 'ghost'">Faculty Fines</flux:button>
            <flux:button wire:click="$set('transactionType', 'borrows')"
                :variant="$transactionType === 'borrows' ? 'primary' : 'ghost'">Borrows</flux:button>
            <flux:button wire:click="$set('transactionType', 'library')"
                :variant="$transactionType === 'library' ? 'primary' : 'ghost'">Library Transactions</flux:button>
        </div>

        {{-- Bulk-action toolbar --}}
        @if(!empty($selectedIds) && $transactionType !== 'all')
        <div class="flex items-center gap-3 px-3 py-2 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
            <flux:text class="text-sm font-medium text-red-700 dark:text-red-300">
                {{ count($selectedIds) }} transaction(s) selected
            </flux:text>
            <flux:spacer />
            <flux:button variant="danger" size="sm" icon="trash"
                x-on:click="$flux.modal('delete-transactions-bulk').show()">
                Delete Selected
            </flux:button>
        </div>
        @endif

        @if($transactionType === 'student_fines')
        {{-- ── Student Fines ── --}}
        <flux:table>
            <flux:table.columns>
                <flux:table.column>
                    <input type="checkbox" wire:model.live="selectAll"
                        class="rounded border-zinc-300 dark:border-zinc-600" />
                </flux:table.column>
                <flux:table.column>Student ID</flux:table.column>
                <flux:table.column>Copy ID</flux:table.column>
                <flux:table.column>Amount</flux:table.column>
                <flux:table.column>Reason</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Date Paid</flux:table.column>
                <flux:table.column>Archived Date</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($transactions as $transaction)
                <flux:table.row>
                    <flux:table.cell>
                        <input type="checkbox" value="{{ $transaction->id }}"
                            wire:model.live="selectedIds"
                            class="rounded border-zinc-300 dark:border-zinc-600" />
                    </flux:table.cell>
                    <flux:table.cell>{{ $transaction->student_id }}</flux:table.cell>
                    <flux:table.cell>{{ $transaction->copy_id }}</flux:table.cell>
                    <flux:table.cell>₱{{ number_format($transaction->amount, 2) }}</flux:table.cell>
                    <flux:table.cell>{{ $transaction->reason }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge :color="$transaction->status == 1 ? 'green' : 'red'">
                            {{ $transaction->status == 1 ? 'Paid' : 'Unpaid' }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $transaction->date_paid ? $transaction->date_paid->format('M d, Y') : 'N/A' }}</flux:table.cell>
                    <flux:table.cell>{{ $transaction->created_at->format('M d, Y') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button icon="trash" size="sm" variant="danger"
                            x-on:click="$wire.set('deletingId', {{ $transaction->id }}); $wire.set('deletingModel', 'student_fine'); $flux.modal('delete-transaction').show()" />
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="9" class="text-center">No Archived Student Fines Found</flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @elseif($transactionType === 'faculty_fines')
        {{-- ── Faculty Fines ── --}}
        <flux:table>
            <flux:table.columns>
                <flux:table.column>
                    <input type="checkbox" wire:model.live="selectAll"
                        class="rounded border-zinc-300 dark:border-zinc-600" />
                </flux:table.column>
                <flux:table.column>Faculty ID</flux:table.column>
                <flux:table.column>Copy ID</flux:table.column>
                <flux:table.column>Amount</flux:table.column>
                <flux:table.column>Reason</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Date Paid</flux:table.column>
                <flux:table.column>Archived Date</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($transactions as $transaction)
                <flux:table.row>
                    <flux:table.cell>
                        <input type="checkbox" value="{{ $transaction->id }}"
                            wire:model.live="selectedIds"
                            class="rounded border-zinc-300 dark:border-zinc-600" />
                    </flux:table.cell>
                    <flux:table.cell>{{ $transaction->faculty_id }}</flux:table.cell>
                    <flux:table.cell>{{ $transaction->copy_id }}</flux:table.cell>
                    <flux:table.cell>₱{{ number_format($transaction->amount, 2) }}</flux:table.cell>
                    <flux:table.cell>{{ $transaction->reason }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge :color="$transaction->status == 1 ? 'green' : 'red'">
                            {{ $transaction->status == 1 ? 'Paid' : 'Unpaid' }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $transaction->date_paid ? $transaction->date_paid->format('M d, Y') : 'N/A' }}</flux:table.cell>
                    <flux:table.cell>{{ $transaction->created_at->format('M d, Y') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button icon="trash" size="sm" variant="danger"
                            x-on:click="$wire.set('deletingId', {{ $transaction->id }}); $wire.set('deletingModel', 'faculty_fine'); $flux.modal('delete-transaction').show()" />
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="9" class="text-center">No Archived Faculty Fines Found</flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @elseif($transactionType === 'library' || $transactionType === 'borrows')
        {{-- ── Borrows / Library ── --}}
        <flux:table>
            <flux:table.columns>
                <flux:table.column>
                    <input type="checkbox" wire:model.live="selectAll"
                        class="rounded border-zinc-300 dark:border-zinc-600" />
                </flux:table.column>
                <flux:table.column>Transaction Name</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Archived Date</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($transactions as $transaction)
                <flux:table.row>
                    <flux:table.cell>
                        <input type="checkbox" value="{{ $transaction->id }}"
                            wire:model.live="selectedIds"
                            class="rounded border-zinc-300 dark:border-zinc-600" />
                    </flux:table.cell>
                    <flux:table.cell>{{ $transaction->name }}</flux:table.cell>
                    <flux:table.cell>
                        @if($transaction->student_borrow_transaction_id)
                            <flux:badge color="blue">Student Borrow</flux:badge>
                        @elseif($transaction->faculty_borrow_transaction_id)
                            <flux:badge color="purple">Faculty Borrow</flux:badge>
                        @elseif($transaction->library_transaction_id)
                            <flux:badge color="green">Library Transaction</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>{{ $transaction->created_at->format('M d, Y') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button icon="trash" size="sm" variant="danger"
                            x-on:click="$wire.set('deletingId', {{ $transaction->id }}); $wire.set('deletingModel', 'transaction'); $flux.modal('delete-transaction').show()" />
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center">No Archived Transactions Found</flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @else
        {{-- ── All ── --}}
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Details</flux:table.column>
                <flux:table.column>Status / Amount</flux:table.column>
                <flux:table.column>Archived Date</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($transactions as $transaction)
                <flux:table.row>
                    <flux:table.cell>
                        @if(isset($transaction->student_id) && isset($transaction->reason))
                            <flux:badge color="blue">Student Fine</flux:badge>
                        @elseif(isset($transaction->faculty_id) && isset($transaction->reason))
                            <flux:badge color="purple">Faculty Fine</flux:badge>
                        @elseif(isset($transaction->student_borrow_transaction_id))
                            <flux:badge color="sky">Student Borrow</flux:badge>
                        @elseif(isset($transaction->faculty_borrow_transaction_id))
                            <flux:badge color="indigo">Faculty Borrow</flux:badge>
                        @else
                            <flux:badge color="green">Library</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        @if(isset($transaction->student_id) && isset($transaction->reason))
                            {{ $transaction->student_id }} &mdash; {{ $transaction->copy_id }}
                        @elseif(isset($transaction->faculty_id) && isset($transaction->reason))
                            {{ $transaction->faculty_id }} &mdash; {{ $transaction->copy_id }}
                        @else
                            {{ $transaction->name ?? 'N/A' }}
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        @if(isset($transaction->amount))
                            ₱{{ number_format($transaction->amount, 2) }} &mdash;
                            <flux:badge :color="$transaction->status == 1 ? 'green' : 'red'" size="sm">
                                {{ $transaction->status == 1 ? 'Paid' : 'Unpaid' }}
                            </flux:badge>
                        @else
                            &mdash;
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>{{ $transaction->created_at->format('M d, Y') }}</flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="4" class="text-center">No Archived Transactions Found</flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @endif

        @if(method_exists($transactions, 'links'))
            {{ $transactions->links() }}
        @endif
    </div>

    {{-- Single delete confirmation modal --}}
    <flux:modal name="delete-transaction" class="max-w-sm">
        <div class="flex flex-col gap-4 p-1">
            <div>
                <flux:heading size="lg">Permanently Delete Transaction</flux:heading>
                <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    This action is <strong>irreversible</strong>. The record will be removed permanently and cannot be recovered.
                </flux:text>
            </div>
            <div class="flex justify-end gap-2">
                <flux:button x-on:click="$flux.modal('delete-transaction').close()">Cancel</flux:button>
                <flux:button variant="danger"
                    wire:click="deleteConfirmed"
                    x-on:click="$flux.modal('delete-transaction').close()">
                    Delete Permanently
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Bulk delete confirmation modal --}}
    <flux:modal name="delete-transactions-bulk" class="max-w-sm">
        <div class="flex flex-col gap-4 p-1">
            <div>
                <flux:heading size="lg">Permanently Delete Selected</flux:heading>
                <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    You are about to permanently delete <strong>{{ count($selectedIds) }} transaction(s)</strong>.
                    This action is <strong>irreversible</strong>.
                </flux:text>
            </div>
            <div class="flex justify-end gap-2">
                <flux:button x-on:click="$flux.modal('delete-transactions-bulk').close()">Cancel</flux:button>
                <flux:button variant="danger"
                    wire:click="deleteSelectedConfirmed"
                    x-on:click="$flux.modal('delete-transactions-bulk').close()">
                    Delete {{ count($selectedIds) }} Record(s)
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
