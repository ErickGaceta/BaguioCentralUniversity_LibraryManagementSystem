<div>
    <flux:header container>
        <flux:heading size="xl">Faculty Fines Management</flux:heading>
        <flux:subheading>
            Daily ₱20 penalties are automatically processed at 10:00 AM for overdue books
        </flux:subheading>

        <flux:spacer />

        <flux:button variant="ghost" icon="arrow-path" x-on:click="$flux.modal('reprocess-confirm').show()">
            Reprocess Penalties
        </flux:button>
        <flux:modal name="reprocess-confirm" class="max-w-lg">
            <flux:heading>Reprocess Penalties</flux:heading>
            <flux:text>
                This will reprocess penalties for all applicable records. This action cannot be undone. Continue?
            </flux:text>
            <div class="flex gap-2 mt-4">
                <flux:button variant="primary" wire:click="reprocessPenalties"
                    x-on:click="$flux.modal('reprocess-confirm').close()">
                    Continue
                </flux:button>
                <flux:button variant="ghost" x-on:click="$flux.modal('reprocess-confirm').close()">
                    Cancel
                </flux:button>
            </div>
        </flux:modal>
    </flux:header>

    <flux:main container class="flex flex-col gap-3">
        <x-flash />

        {{-- Filters --}}
        <div class="flex gap-4 mb-6">
            <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Search by faculty name or ID..."
                class="flex-1" />

            <flux:select wire:model.live="statusFilter">
                <option value="all">All Fines</option>
                <option value="unpaid">Unpaid</option>
                <option value="paid">Paid</option>
            </flux:select>
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
                    <flux:heading size="sm" class="text-blue-900 dark:text-blue-100 mb-1">Automatic Penalties
                    </flux:heading>
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
                <flux:table.column>Faculty ID</flux:table.column>
                <flux:table.column>Faculty Name</flux:table.column>
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
                        <flux:table.cell>{{ $fine->faculty->faculty_id }}</flux:table.cell>
                        <flux:table.cell>
                            {{ $fine->faculty->first_name }}
                            {{ $fine->faculty->middle_name }}
                            {{ $fine->faculty->last_name }}
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
                                    <flux:button size="sm" variant="primary" icon="currency-dollar"
                                        x-on:click="$flux.modal('payment').show(); $wire.openPaymentModal({{ $fine->id }})">
                                        Pay
                                    </flux:button>
                                @endif

                                <flux:button size="sm" variant="danger" icon="archive-box-arrow-down"
                                    x-on:click="$flux.modal('archive-fine').show(); $wire.set('archivingFineId', {{ $fine->id }})">
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

        <x-pagination :paginator="$fines" />
    </flux:main>

    <x-confirm-modal name="archive-fine" title="Archive Fine"
        description="This fine will be saved to the archive and removed from this list. Continue?" confirm-label="Archive"
        confirm-variant="danger" confirm-action="archiveConfirmed" />

    <x-pay-modal :amount="$paymentAmount" />
</div>
