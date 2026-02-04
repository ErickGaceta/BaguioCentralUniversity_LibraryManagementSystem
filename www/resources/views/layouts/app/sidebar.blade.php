<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar container class="border-b border-zinc-200" style="background-color: #860805;">
        <flux:sidebar.header>
            <flux:avatar circle size="xl" src="{{ asset('favicon.ico') }}" />
            <div>
                <flux:text class="text-xs" align="center" style="color: #eae0d2;">Baguio Central University</flux:text>
                <flux:text class="text-xs" align="center" style="color: #eae0d2;">Library Management System</flux:text>
            </div>
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.item :current="request()->routeIs('dashboard')" icon="squares-2x2"
                href="{{ route('dashboard') }}">Dashboard</flux:sidebar.item>

            <flux:sidebar.spacer />

            <flux:sidebar.group expandable icon="book-open"
                :expanded="request()->routeIs('books.*') || request()->routeIs('copies.*')" heading="Bookshelf">
                <flux:sidebar.item :current="request()->routeIs('books.*')" href="{{ route('books.index') }}"
                    icon:trailing="square-3-stack-3d">
                    Books
                </flux:sidebar.item>
                <flux:sidebar.item :current="request()->routeIs('copies.*')" href="{{ route('copies.index') }}"
                    icon:trailing="document-duplicate">
                    Copies
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.spacer />

            <flux:sidebar.group expandable icon="circle-stack"
                :expanded="request()->routeIs('transactions.*') || request()->routeIs('library.*')"
                heading="Transactions">
                <flux:sidebar.item :current="request()->routeIs('transactions.borrow.*')"
                    href="{{ route('transactions.borrow.index') }}" icon:trailing="folder-arrow-down">
                    Issuance
                </flux:sidebar.item>
                <flux:sidebar.item :current="request()->routeIs('transactions.library.*')"
                    href="{{ route('transactions.library.index') }}" icon:trailing="building-library">
                    Library
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.spacer />

            <flux:sidebar.group expandable icon="user-group"
                :expanded="request()->routeIs('students.*') || request()->routeIs('faculty.*')" heading="Users">
                <flux:sidebar.item :current="request()->routeIs('students.*')" href="{{ route('students.index') }}"
                    icon:trailing="user-circle">
                    Students
                </flux:sidebar.item>
                <flux:sidebar.item :current="request()->routeIs('faculty.*')" href="{{ route('faculty.index') }}"
                    icon:trailing="users">
                    Faculties
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.spacer />

            <flux:sidebar.group expandable icon="viewfinder-circle"
                :expanded="request()->routeIs('fines.students.*') || request()->routeIs('fines.faculty.*')"
                heading="Fines">
                <flux:sidebar.item :current="request()->routeIs('fines.students.*')"
                    href="{{ route('fines.students.index') }}" icon:trailing="magnifying-glass-circle">
                    Students
                </flux:sidebar.item>
                <flux:sidebar.item :current="request()->routeIs('fines.faculty.*')"
                    href="{{ route('fines.faculty.index') }}" icon:trailing="user-minus">
                    Faculties
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.spacer />

            <flux:sidebar.group expandable icon="archive-box-arrow-down"
                :expanded="request()->routeIs('archives.books.*') || request()->routeIs('archives.transactions.*') || request()->routeIs('archives.users.*')"
                heading="Archives">
                <flux:sidebar.item :current="request()->routeIs('archives.books.*')"
                    href="{{ route('archives.books.index') }}" icon:trailing="book-open">
                    Books
                </flux:sidebar.item>
                <flux:sidebar.item :current="request()->routeIs('archives.transactions.*')"
                    href="{{ route('archives.transactions.index') }}" icon:trailing="circle-stack">
                    Transactions
                </flux:sidebar.item>
                <flux:sidebar.item :current="request()->routeIs('archives.users.*')"
                    href="{{ route('archives.users.index') }}" icon:trailing="user-group">
                    Users
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.spacer />

            <flux:sidebar.item :current="request()->routeIs('generate.*')" icon="flag"
                href="{{ route('generate.index') }}">Reports</flux:sidebar.item>
        </flux:sidebar.nav>

        <flux:sidebar.spacer />

        <flux:sidebar.nav>
            <flux:sidebar.item icon:trailing="clock">
                {{ now()->format('h:i A') }}
            </flux:sidebar.item>
        </flux:sidebar.nav>

    </flux:sidebar>

    {{ $slot }}

    @fluxScripts
    <script>
    function updateTime() {
        const now = new Date();
        document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
    }

    updateTime();
    setInterval(updateTime, 60000); // Update every minute
    </script>
</body>

</html>