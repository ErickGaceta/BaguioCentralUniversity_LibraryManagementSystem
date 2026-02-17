    <flux:sidebar sticky container class="border-b border-zinc-200" style="background-color: #860805;">
        <flux:sidebar.header class="flex flex-col">
            <flux:avatar circle size="xl" src="{{ asset('favicon.ico') }}" />
            <div>
                <flux:text class="text-sm" align="center" style="color: #eae0d2;">Baguio Central University</flux:text>
                <flux:text class="text-xs" align="center" color="yellow">Library Management System</flux:text>
            </div>
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.item icon="squares-2x2" :current="request()->routeIs('dashboard')" href="{{ route('dashboard') }}">Dashboard</flux:sidebar.item>

            <flux:sidebar.spacer />

            <flux:sidebar.group expandable :expanded="request()->routeIs('books.*') || request()->routeIs('copies.*')" icon="book-open" heading="Bookshelf">
                <flux:sidebar.item :href="route('books.index')" :current="request()->routeIs('books.*')"
                    icon:trailing="square-3-stack-3d">
                    Books
                </flux:sidebar.item>
                <flux:sidebar.item
                    icon:trailing="document-duplicate" :href="route('copies.index')" :current="request()->routeIs('copies.index')">
                    Copies
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.spacer />

            <flux:sidebar.group expandable icon="circle-stack" :expanded="request()->routeIs('transactions.*')"
                heading="Transactions">
                <flux:sidebar.item icon:trailing="folder-arrow-down" :href="route('transactions.issuance')" :current="request()->routeIs('transactions.issuance')">
                    Issuance
                </flux:sidebar.item>
                <flux:sidebar.item icon:trailing="building-library" :href="route('transactions.library')" :current="request()->routeIs('transactions.library')">
                    Library
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.spacer />

            <flux:sidebar.group expandable icon="user-group" heading="Users" :expanded="request()->routeIs('users.*')">
                <flux:sidebar.item
                    icon:trailing="user-circle" :href="route('users.students-index')" :current="request()->routeIs('users.students-index') || request()->routeIs('users.students-create') || request()->routeIs('users.students-edit')">
                    Students
                </flux:sidebar.item>
                <flux:sidebar.item
                    icon:trailing="users" :href="route('users.faculties-index')" :current="request()->routeIs('users.faculties-index') || request()->routeIs('users.faculties-create') || request()->routeIs('users.faculties-edit')">
                    Faculties
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.spacer />

            <flux:sidebar.group expandable icon="viewfinder-circle" :expanded="request()->routeIs('fines.*')"
                heading="Fines">
                <flux:sidebar.item icon:trailing="magnifying-glass-circle" :href="route('fines.student-fines')" :current="request()->routeIs('fines.student-fines')">
                    Students
                </flux:sidebar.item>
                <flux:sidebar.item icon:trailing="user-minus" :href="route('fines.faculty-fines')" :current="request()->routeIs('fines.faculty-fines')">
                    Faculties
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.spacer />

            <flux:sidebar.group expandable icon="archive-box-arrow-down" :expanded="request()->routeIs('archives.*')"
                heading="Archives">
                <flux:sidebar.item icon:trailing="book-open" :href="route('archives.archives-library')" :current="request()->routeIs('archives.archives-library')">
                    Books
                </flux:sidebar.item>
                <flux:sidebar.item icon:trailing="circle-stack" :href="route('archives.archives-transactions')" :current="request()->routeIs('archives.archives-transactions')">
                    Transactions
                </flux:sidebar.item>
                <flux:sidebar.item icon:trailing="user-group" :href="route('archives.archives-users')" :current="request()->routeIs('archives.archives-users')">
                    Users
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.spacer />

            <flux:sidebar.item icon="flag" :href="route('reports.reports-index')" :current="request()->routeIs('reports.*')">Reports</flux:sidebar.item>
        </flux:sidebar.nav>

        <flux:sidebar.spacer />

    </flux:sidebar>
