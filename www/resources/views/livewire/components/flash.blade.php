<div style="background:transparent; position: absolute; bottom: 5px; right: 10px;">
    @if(session()->has('success') || session()->has('error') || session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">

            @if(session()->has('success'))
                <flux:card class="flex gap-3 items-center border-l-4 border-green-500">
                    <flux:icon.check-circle class="text-green-500 shrink-0" />
                    <flux:separator vertical />
                    <div class="flex flex-col flex-1">
                        <flux:heading>Success!</flux:heading>
                        <flux:text>{{ session('success') }}</flux:text>
                    </div>
                    <flux:button variant="ghost" size="sm" icon="x-mark" x-on:click="show = false" />
                </flux:card>
            @endif

            @if(session()->has('message'))
                <flux:card class="flex gap-3 items-center border-l-4 border-sky-500">
                    <flux:icon.information-circle class="text-sky-500 shrink-0" />
                    <flux:separator vertical />
                    <div class="flex flex-col flex-1">
                        <flux:heading>Notice</flux:heading>
                        <flux:text>{{ session('message') }}</flux:text>
                    </div>
                    <flux:button variant="ghost" size="sm" icon="x-mark" x-on:click="show = false" />
                </flux:card>
            @endif

            @if(session()->has('error'))
                <flux:card class="flex gap-3 items-center border-l-4 border-red-600">
                    <flux:icon.x-circle class="text-red-600 shrink-0" />
                    <flux:separator vertical />
                    <div class="flex flex-col flex-1">
                        <flux:heading>Error!</flux:heading>
                        <flux:text>{{ session('error') }}</flux:text>
                    </div>
                    <flux:button variant="ghost" size="sm" icon="x-mark" x-on:click="show = false" />
                </flux:card>
            @endif

        </div>
    @endif
</div>
