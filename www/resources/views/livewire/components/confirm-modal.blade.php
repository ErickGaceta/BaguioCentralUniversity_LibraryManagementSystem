@props([
    'name' => 'confirm-action',
    'title' => 'Are you sure?',
    'description' => 'This action cannot be undone. Continue?',
    'confirmLabel' => 'Confirm',
    'confirmVariant' => 'danger',
    'confirmAction' => 'confirmed',
])
<flux:modal :name="$name" class="max-w-lg">
    <flux:heading>{{ $title }}</flux:heading>
    <flux:text>{{ $description }}</flux:text>
    <div class="flex gap-2 mt-4 justify-end w-full">
        <flux:button variant="ghost" x-on:click="$flux.modal('{{ $name }}').close()">
            Cancel
        </flux:button>
        <flux:button
            :variant="$confirmVariant"
            wire:click="{{ $confirmAction }}"
            x-on:click="$flux.modal('{{ $name }}').close()">
            {{ $confirmLabel }}
        </flux:button>
    </div>
</flux:modal>
