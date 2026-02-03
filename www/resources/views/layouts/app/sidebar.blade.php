<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <img src="{{ asset('favicon.ico') }}" alt="">
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.item icon="squares-2x2" href="{{ route('dashboard') }}">Dashboard</flux:sidebar.item>

            <flux:sidebar.spacer />

            <flux:sidebar.group expandable icon="book-open" :expanded="false" heading="Bookshelf" class="grid">
                <flux:sidebar.item href="#" icon:trailing="square-3-stack-3d">Books</flux:sidebar.item>
                <flux:sidebar.item href="#" icon:trailing="document-duplicate">Copies</flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.spacer />

            <flux:sidebar.group expandable icon="circle-stack" :expanded="false" heading="Transactions" class="grid">
                <flux:sidebar.item href="#" icon:trailing="folder-arrow-down">Issuance</flux:sidebar.item>
                <flux:sidebar.item href="#" icon:trailing="building-library">Library</flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.spacer />

            <flux:sidebar.group expandable icon="user-group" :expanded="false" heading="Users" class="grid">
                <flux:sidebar.item href="#" icon:trailing="user-circle">Students</flux:sidebar.item>
                <flux:sidebar.item href="#" icon:trailing="users">Faculties</flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.spacer />

            <flux:sidebar.group expandable icon="viewfinder-circle" :expanded="false" heading="Fines" class="grid">
                <flux:sidebar.item href="#" icon:trailing="magnifying-glass-circle">Students</flux:sidebar.item>
                <flux:sidebar.item href="#" icon:trailing="user-minus">Faculties</flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.spacer />

            <flux:sidebar.group expandable icon="archive-box-arrow-down" :expanded="false" heading="Archives"
                class="grid">
                <flux:sidebar.item href="#" icon:trailing="book-open">Books</flux:sidebar.item>
                <flux:sidebar.item href="#" icon:trailing="circle-stack">Transactions</flux:sidebar.item>
                <flux:sidebar.item href="#" icon:trailing="user-group">Users</flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.spacer />

            <flux:sidebar.item icon="flag" href="{{ route('generate.index') }}">Reports</flux:sidebar.item>
        </flux:sidebar.nav>
    </flux:sidebar>

    {{ $slot }}

    @fluxScripts
</body>

</html>