<div class="w-full flex flex-col gap-4 p-3">
    <div>
        <flux:heading size="xl">Dashboard</flux:heading>
        <flux:text>Library Statistics</flux:text>
    </div>

    <div class="grid gap-4" style="grid-template-columns: repeat(3, 1fr);">
        <flux:card class="flex flex-col gap-3">
            <flux:heading>Total Books</flux:heading>
            <flux:separator />
            <flux:text size="xl">{{ $stats['total_books'] }}</flux:text>
        </flux:card>

        <flux:card class="flex flex-col gap-3">
            <flux:heading>Total Students</flux:heading>
            <flux:separator />
            <flux:text size="xl">{{ $stats['total_students'] }}</flux:text>
        </flux:card>

        <flux:card class="flex flex-col gap-3">
            <flux:heading>Total Faculties</flux:heading>
            <flux:separator />
            <flux:text size="xl">{{ $stats['total_faculties'] }}</flux:text>
        </flux:card>

        <flux:card class="flex flex-col gap-3">
            <flux:heading>Total Fines</flux:heading>
            <flux:separator />
            <flux:text size="xl">₱ {{ $stats['total_fines'] }}.00</flux:text>
        </flux:card>

        <flux:card class="flex flex-col gap-3">
            <flux:heading>Books Issued</flux:heading>
            <flux:separator />
            <flux:text size="xl">{{ $stats['books_issued'] }}</flux:text>
        </flux:card>

        <flux:card class="flex flex-col gap-3">
            <flux:heading>Overdue Books</flux:heading>
            <flux:separator />
            <flux:text size="xl">{{ $stats['overdue_books'] }}</flux:text>
        </flux:card>
    </div>

    <div style="padding-top: 20px;">
        <flux:table>
            <flux:heading variant="subtle" class="flex items-center justify-center my-2">Recent Transactions</flux:heading>
            <flux:table.columns>
                <flux:table.column>Transaction</flux:table.column>
                <flux:table.column>Date || Time</flux:table.column>
                <flux:table.column>Made By</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($recentTransactions as $rt)
                <flux:table.row>
                    <flux:table.cell>{{ $rt->transaction_name }}</flux:table.cell>
                    <flux:table.cell>{{ \Carbon\Carbon::parse($rt->date)->format('Y-m-d || h:i A') }}</flux:table.cell>
                    <flux:table.cell>{{ $rt->made_by }}</flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="3" class="text-center">No Recent Transactions</flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
