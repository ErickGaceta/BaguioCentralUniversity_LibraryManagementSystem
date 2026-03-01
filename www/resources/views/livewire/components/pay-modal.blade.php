@props(['amount' => null])

<flux:modal name="payment" class="max-w-lg" x-on:close-payment-modal.window="$flux.modal('payment').close()">
    <flux:heading>Confirm Payment</flux:heading>
    <flux:text>Are you sure you want to mark this fine as paid?</flux:text>

    @if($amount)
        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg mt-4">
            <flux:text size="sm" class="text-gray-500">Amount to pay:</flux:text>
            <flux:heading size="lg" class="text-green-600">₱{{ number_format($amount, 2) }}</flux:heading>
        </div>
    @endif

    <div class="flex gap-2 mt-4 justify-end">
        <flux:button x-on:click="$flux.modal('payment').close()" variant="ghost">Cancel</flux:button>
        <flux:button wire:click="markAsPaid" variant="primary">Confirm Payment</flux:button>
    </div>
</flux:modal>
