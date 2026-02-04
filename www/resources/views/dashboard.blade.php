<x-layouts::app :title="('__Dashboard')">
    <div class="flex flex-col w-full mx-auto gap-2">
        <div class="mb-4">
            <flux:heading size="xl">Dashboard</flux:heading>
        </div>

        <div class="flex flex-wrap gap-3 w-full">
            <flux:card class="grow">
                <flux:heading class="py-2" size="lg">Total Books</flux:heading>
                <flux:separator class="mx-auto" />
                <flux:text class="mt-2 mb-4" color="blue" variant="strong">
                    {{ $totalBooks }}
                </flux:text>
                <flux:button :href="route('books.index')" size="xs" variant="ghost" icon:trailing="arrow-long-right">
                    View
                </flux:button>
            </flux:card>

            <flux:card class="grow">
                <flux:heading class="py-2" size="lg">Total Students</flux:heading>
                <flux:separator class="mx-auto" />
                <flux:text class="mt-2 mb-4" color="blue" variant="strong">
                    {{ $totalStudents }}
                </flux:text>
                <flux:button size="xs" variant="ghost" icon:trailing="arrow-long-right" align="end">View</flux:button>
            </flux:card>

            <flux:card class="grow">
                <flux:heading class="py-2" size="lg">Total Borrows</flux:heading>
                <flux:separator class="mx-auto" />
                <flux:text class="mt-2 mb-4" color="blue" variant="strong">
                    {{ $totalBorrows }}
                </flux:text>
                <flux:button size="xs" variant="ghost" icon:trailing="arrow-long-right" align="end">View</flux:button>
            </flux:card>
        </div>

        <div class="flex flex-wrap gap-3 w-full">
            <flux:card class="flex-1">
                <flux:heading class="py-2" size="lg">Total Copies</flux:heading>
                <flux:separator class="mx-auto" />
                <flux:text class="mt-2 mb-4" color="blue" variant="strong">
                    {{ $totalCopies }}
                </flux:text>
                <flux:button size="xs" variant="ghost" icon:trailing="arrow-long-right" align="end">View
                </flux:button>
            </flux:card>

            <flux:card class="flex-1">
                <flux:heading class="py-2" size="lg">Total Faculties</flux:heading>
                <flux:separator class="mx-auto" />
                <flux:text class="mt-2 mb-4 mx-auto" color="blue" variant="strong">
                    {{ $totalFaculties }}
                </flux:text>
                <flux:button size="xs" variant="ghost" icon:trailing="arrow-long-right" align="end">View</flux:button>
            </flux:card>

            <flux:card class="flex-1">
                <flux:heading class="py-2" size="lg">Total Fines</flux:heading>
                <flux:separator class="mx-auto" />
                <flux:text class="mt-2 mb-4" color="blue" variant="strong">
                    {{ $totalFines }}
                </flux:text>
                <flux:button size="xs" variant="ghost" icon:trailing="arrow-long-right" align="end">View</flux:button>
            </flux:card>
        </div>

        <div class="mt-4">
            <flux:text size="lg" class="mb-4">Recent Transactions</flux:text>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Transaction Name</flux:table.column>
                    <flux:table.column>Date</flux:table.column>
                    <flux:table.column>Transaction Type</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($recentTransactions as $transaction)
                    <flux:table.row>
                        <flux:table.cell>{{ $transaction['user_name'] }}</flux:table.cell>
                        <flux:table.cell>{{ $transaction['transaction_date']->format('M d, Y h:i A') }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{ $transaction['status_color'] }}" size="sm" inset="top bottom">
                                {{ $transaction['transaction_type'] }}
                            </flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                    @empty
                    <flux:table.row>
                        <flux:table.cell colspan="3" class="text-center">No recent transactions</flux:table.cell>
                    </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</x-layouts::app>