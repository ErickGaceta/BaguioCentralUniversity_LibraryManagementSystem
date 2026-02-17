<div>
    <flux:header container>
        <flux:heading size="xl">Student Fines Management</flux:heading>
        <flux:subheading>
            Daily ₱20 penalties are automatically processed at 10:00 AM for overdue books
        </flux:subheading>

        <flux:spacer />

        <flux:button
            wire:click="reprocessPenalties"
            variant="ghost"
            icon="arrow-path"
            wire:confirm="This will reprocess penalties. Continue?">
            Reprocess Penalties
        </flux:button>
    </flux:header>

    <flux:main container class="flex flex-col gap-3">
        @if (session()->has('success'))
        <flux:card class="mb-6 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500">
            <div class="flex gap-3 items-center">
                <flux:icon.check-circle class="text-green-600 dark:text-green-400 w-6 h-6" />
                <flux:text class="text-green-800 dark:text-green-200">
                    {{ session('success') }}
                </flux:text>
            </div>
        </flux:card>
        @endif

        {{-- Filters --}}
        <div class="flex gap-4 mb-6">
            <flux:input
                wire:model.live="search"
                icon="magnifying-glass"
                placeholder="Search by student name or ID..."
                class="flex-1" />

            <div>
                <flux:select wire:model.live="statusFilter">
                    <option value="all">All Fines</option>
                    <option value="unpaid">Unpaid</option>
                    <option value="paid">Paid</option>
                </flux:select>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="flex gap-4 mb-6">
            <flux:card class="grow">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:text size="sm" class="text-gray-500">Total Unpaid</flux:text>
                        <flux:heading size="lg">₱{{ number_format($totalUnpaid, 2) }}</flux:heading>
                        <flux:text size="xs" class="text-gray-500">{{ $countUnpaid }} unpaid fine(s)</flux:text>
                    </div>
                    <flux:icon.exclamation-circle class="w-10 h-10" />
                </div>
            </flux:card>

            <flux:card class="grow">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:text size="sm" class="text-gray-500">Total Paid</flux:text>
                        <flux:heading size="lg">₱{{ number_format($totalPaid, 2) }}</flux:heading>
                    </div>
                    <flux:icon.check-circle class="w-10 h-10" />
                </div>
            </flux:card>

            <flux:card class="grow">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:text size="sm" class="text-gray-500">Total Fines</flux:text>
                        <flux:heading size="lg">{{ $fines->total() }}</flux:heading>
                        <flux:text size="xs" class="text-gray-500">All time</flux:text>
                    </div>
                    <flux:icon.document-text class="w-10 h-10" />
                </div>
            </flux:card>
        </div>

        {{-- Info Notice --}}
        <flux:card class="mb-6 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500">
            <div class="flex gap-3 items-start">
                <flux:icon.information-circle class="text-blue-600 dark:text-blue-400 w-6 h-6 shrink-0 mt-0.5" />
                <div>
                    <flux:heading size="sm" class="text-blue-900 dark:text-blue-100 mb-1">Automatic Penalties</flux:heading>
                    <flux:text class="text-blue-800 dark:text-blue-200">
                        ₱20 daily overdue penalty is added automatically at 10:00 AM for each day a book is overdue.
                        Damage fines are automatically added when a book is returned with damage.
                    </flux:text>
                </div>
            </div>
        </flux:card>

        {{-- Fines Table --}}
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Student ID</flux:table.column>
                <flux:table.column>Student Name</flux:table.column>
                <flux:table.column>Book/Copy</flux:table.column>
                <flux:table.column>Amount</flux:table.column>
                <flux:table.column>Reason</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Date Issued</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($fines as $fine)
                <flux:table.row :key="$fine->id">
                    <flux:table.cell>{{ $fine->student->student_id }}</flux:table.cell>
                    <flux:table.cell>
                        {{ $fine->student->first_name }}
                        {{ $fine->student->middle_name }}
                        {{ $fine->student->last_name }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="text-sm">
                            <div class="font-medium">{{ $fine->copy->book->title ?? 'N/A' }}</div>
                            <div class="text-gray-500">Copy: {{ $fine->copy_id }}</div>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge variant="warning">₱{{ number_format($fine->amount, 2) }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $fine->reason }}</flux:table.cell>
                    <flux:table.cell>
                        @if ($fine->isPaid())
                        <flux:badge icon="check-circle">Paid</flux:badge>
                        @else
                        <flux:badge icon="exclamation-circle">Unpaid</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>{{ $fine->created_at->format('M d, Y') }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex gap-2">
                            @if (!$fine->isPaid())
                            <flux:button
                                wire:click="openPaymentModal({{ $fine->id }})"
                                size="sm"
                                variant="primary"
                                icon="currency-dollar">
                                Pay
                            </flux:button>
                            @endif

                            <flux:button
                                wire:click="archiveFine({{ $fine->id }})"
                                wire:confirm="Archive this fine for {{ $fine->student->first_name }} {{ $fine->student->last_name }}? It will be saved to the archive and removed from this list."
                                size="sm"
                                variant="danger"
                                icon="archive-box-arrow-down">
                                Archive
                            </flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center text-gray-500">
                        No fines found.
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $fines->links() }}
        </div>
    </flux:main>

    {{-- Payment Modal --}}
    <flux:modal wire:model="showPaymentModal" name="payment">
        <flux:heading>Confirm Payment</flux:heading>

        <div>
            <div class="space-y-4">
                <flux:text>Are you sure you want to mark this fine as paid?</flux:text>

                @if($paymentAmount)
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                    <flux:text size="sm" class="text-gray-500">Amount to pay:</flux:text>
                    <flux:heading size="lg" class="text-green-600">₱{{ number_format($paymentAmount, 2) }}</flux:heading>
                </div>
                @endif
            </div>
        </div>

        <div>
            <flux:button wire:click="closePaymentModal" variant="ghost">Cancel</flux:button>
            <flux:button wire:click="markAsPaid" variant="primary">Confirm Payment</flux:button>
        </div>
    </flux:modal>
</div>
