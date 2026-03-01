@props([
    'name' => 'restore-action',
    'title' => 'Restore Item',
    'description' => 'Are you sure you want to restore this item?',
    'confirmAction' => 'restoreConfirmed',
])

<flux:modal :name="$name" class="max-w-lg">
    <flux:heading size="lg">{{ $title }}</flux:heading>
    <flux:separator/>
    <flux:text class="py-2">{{ $description }}</flux:text>
    <div class="flex gap-2 mt-4 justify-end">
        <flux:button variant="ghost" x-on:click="$flux.modal('{{ $name }}').close()">
            Cancel
        </flux:button>
        <flux:button
            variant="primary"
            color="emerald"
            wire:click="{{ $confirmAction }}"
            x-on:click="$flux.modal('{{ $name }}').close()">
            Restore
        </flux:button>
    </div>
</flux:modal>
