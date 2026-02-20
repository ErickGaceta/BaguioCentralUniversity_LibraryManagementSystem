@include('partial.loading-screen')

@include('partial.header')

@include('partial.sidebar')

<main class="w-full">
    {{ $slot }}
</main>


@include('partial.footer')
