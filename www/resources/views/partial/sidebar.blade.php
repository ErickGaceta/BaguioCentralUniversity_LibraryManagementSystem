<style>
    #bcu-sidebar,
    #bcu-sidebar * {
        --color-zinc-800: #eae0d2;
        --color-zinc-700: #eae0d2;
        --color-zinc-600: #eae0d2;
        --color-zinc-500: #c9bfb0;
        --color-zinc-400: #c9bfb0;
        --color-zinc-300: #c9bfb0;
        --color-zinc-200: rgba(234, 224, 210, 0.15);
        --color-zinc-100: rgba(234, 224, 210, 0.1);
        --color-zinc-50: rgba(234, 224, 210, 0.08);
        color-scheme: dark;
    }

    /* Direct element targeting as a hard fallback */
    #bcu-sidebar a,
    #bcu-sidebar button,
    #bcu-sidebar span,
    #bcu-sidebar li {
        color: #eae0d2 !important;
    }

    #bcu-sidebar svg {
        color: #eae0d2 !important;
    }

    #bcu-sidebar a:hover,
    #bcu-sidebar button:hover {
        background-color: rgba(255, 174, 0, 0.1) !important;
        color: #ffffff !important;
    }

    #bcu-sidebar a:hover svg,
    #bcu-sidebar button:hover svg {
        color: #ffffff !important;
    }

    #bcu-sidebar a[data-current],
    #bcu-sidebar [aria-current="page"] {
        background-color: #ff040046 !important;
        color: #ffffff !important;
    }

    #bcu-sidebar [data-flux-sidebar-group]>button,
    #bcu-sidebar [data-flux-sidebar-heading] {
        color: #eae0d2 !important;
        opacity: 1 !important;
    }
</style>

<flux:sidebar id="bcu-sidebar" sticky container class="border-b border-zinc-200"
    style="background-color: #860805; color-scheme: dark;">
    <flux:sidebar.header class="flex flex-col">
        <flux:avatar circle size="xl" src="{{ asset('favicon.ico') }}" />
        <div>
            <flux:text class="text-sm" align="center" style="color: #eae0d2;">Baguio Central University</flux:text>
            <flux:text class="text-xs" align="center" color="yellow">Library Management System</flux:text>
        </div>
    </flux:sidebar.header>

    <flux:sidebar.nav>
        <flux:sidebar.item icon="squares-2x2" :current="request()->routeIs('dashboard')" href="{{ route('dashboard') }}"
            wire:navigate>
            Dashboard
        </flux:sidebar.item>

        <flux:sidebar.spacer />

        <flux:sidebar.group expandable :expanded="request()->routeIs('books.*') || request()->routeIs('copies.*')"
            icon="book-open" heading="Bookshelf">
            <flux:sidebar.item :href="route('books.index')" :current="request()->routeIs('books.*')"
                icon:trailing="square-3-stack-3d" wire:navigate>
                Books
            </flux:sidebar.item>
            <flux:sidebar.item icon:trailing="document-duplicate" :href="route('copies.index')"
                :current="request()->routeIs('copies.index')" wire:navigate>
                Copies
            </flux:sidebar.item>
        </flux:sidebar.group>

        <flux:sidebar.spacer />

        <flux:sidebar.group expandable icon="circle-stack" :expanded="request()->routeIs('transactions.*')"
            heading="Transactions">
            <flux:sidebar.item icon:trailing="folder-arrow-down" :href="route('transactions.issuance')"
                :current="request()->routeIs('transactions.issuance')" wire:navigate>
                Issuance
            </flux:sidebar.item>
            <flux:sidebar.item icon:trailing="building-library" :href="route('transactions.library')"
                :current="request()->routeIs('transactions.library')" wire:navigate>
                Library
            </flux:sidebar.item>
        </flux:sidebar.group>

        <flux:sidebar.spacer />

        <flux:sidebar.group expandable icon="user-group" heading="Users" :expanded="request()->routeIs('users.*')">
            <flux:sidebar.item icon:trailing="user-circle" :href="route('users.students-index')"
                :current="request()->routeIs('users.students-index') || request()->routeIs('users.students-create') || request()->routeIs('users.students-edit')"
                wire:navigate>
                Students
            </flux:sidebar.item>
            <flux:sidebar.item icon:trailing="users" :href="route('users.faculties-index')"
                :current="request()->routeIs('users.faculties-index') || request()->routeIs('users.faculties-create') || request()->routeIs('users.faculties-edit')"
                wire:navigate>
                Faculties
            </flux:sidebar.item>
        </flux:sidebar.group>

        <flux:sidebar.spacer />

        <flux:sidebar.group expandable icon="viewfinder-circle" :expanded="request()->routeIs('fines.*')"
            heading="Fines">
            <flux:sidebar.item wire:navigate icon:trailing="magnifying-glass-circle"
                :href="route('fines.student-fines')" :current="request()->routeIs('fines.student-fines')">
                Students
            </flux:sidebar.item>
            <flux:sidebar.item wire:navigate icon:trailing="user-minus" :href="route('fines.faculty-fines')"
                :current="request()->routeIs('fines.faculty-fines')">
                Faculties
            </flux:sidebar.item>
        </flux:sidebar.group>

        <flux:sidebar.spacer />

        <flux:sidebar.group expandable icon="archive-box-arrow-down" :expanded="request()->routeIs('archives.*')"
            heading="Archives">
            <flux:sidebar.item wire:navigate icon:trailing="book-open" :href="route('archives.archives-library')"
                :current="request()->routeIs('archives.archives-library')">
                Books
            </flux:sidebar.item>
            <flux:sidebar.item wire:navigate icon:trailing="circle-stack"
                :href="route('archives.archives-transactions')"
                :current="request()->routeIs('archives.archives-transactions')">
                Transactions
            </flux:sidebar.item>
            <flux:sidebar.item wire:navigate icon:trailing="user-group" :href="route('archives.archives-users')"
                :current="request()->routeIs('archives.archives-users')">
                Users
            </flux:sidebar.item>
        </flux:sidebar.group>

        <flux:sidebar.spacer />

        <flux:sidebar.item wire:navigate icon="flag" :href="route('reports.reports-index')"
            :current="request()->routeIs('reports.*')">
            Reports
        </flux:sidebar.item>
    </flux:sidebar.nav>

    <flux:sidebar.spacer />

</flux:sidebar>
