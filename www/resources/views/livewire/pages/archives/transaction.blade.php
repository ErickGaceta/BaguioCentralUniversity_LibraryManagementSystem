<div class="w-full flex flex-col gap-4 p-3">
    <div>
        <flux:heading size="xl">Transaction Archive</flux:heading>
        <flux:text>Archived transactions from the library system</flux:text>
    </div>

    <div class="flex flex-col gap-4">
        <div class="flex gap-2">
            <flux:button
                wire:click="$set('transactionType', 'all')"
                :variant="$transactionType === 'all' ? 'primary' : 'ghost'">
                All
            </flux:button>
            <flux:button
                wire:click="$set('transactionType', 'student_fines')"
                :variant="$transactionType === 'student_fines' ? 'primary' : 'ghost'">
                Student Fines
            </flux:button>
            <flux:button
                wire:click="$set('transactionType', 'faculty_fines')"
                :variant="$transactionType === 'faculty_fines' ? 'primary' : 'ghost'">
                Faculty Fines
            </flux:button>
            <flux:button
                wire:click="$set('transactionType', 'borrows')"
                :variant="$transactionType === 'borrows' ? 'primary' : 'ghost'">
                Borrows
            </flux:button>
            <flux:button
                wire:click="$set('transactionType', 'library')"
                :variant="$transactionType === 'library' ? 'primary' : 'ghost'">
                Library Transactions
            </flux:button>
        </div>

        @if($transactionType === 'student_fines')
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Student ID</flux:table.column>
                <flux:table.column>Copy ID</flux:table.column>
                <flux:table.column>Amount</flux:table.column>
                <flux:table.column>Reason</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Date Paid</flux:table.column>
                <flux:table.column>Archived Date</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($transactions as $transaction)
                <flux:table.row>
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
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center">
                        No Archived Student Fines Found
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @elseif($transactionType === 'faculty_fines')
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Faculty ID</flux:table.column>
                <flux:table.column>Copy ID</flux:table.column>
                <flux:table.column>Amount</flux:table.column>
                <flux:table.column>Reason</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Date Paid</flux:table.column>
                <flux:table.column>Archived Date</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($transactions as $transaction)
                <flux:table.row>
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
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center">
                        No Archived Faculty Fines Found
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @elseif($transactionType === 'library' || $transactionType === 'borrows')
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Transaction Name</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Archived Date</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($transactions as $transaction)
                <flux:table.row>
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
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="3" class="text-center">
                        No Archived Transactions Found
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @else
        <flux:text>Showing all archived transactions. Use the filters above to view specific types.</flux:text>
        @endif

        @if(method_exists($transactions, 'links'))
        {{ $transactions->links() }}
        @endif
    </div>
</div>
