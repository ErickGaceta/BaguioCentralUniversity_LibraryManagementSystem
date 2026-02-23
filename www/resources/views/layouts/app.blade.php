@include('partial.loading-screen')

@include('partial.header')

<livewire:layouts.sidebar />

<main class="w-full">
    {{ $slot }}
</main>


@include('partial.footer')
