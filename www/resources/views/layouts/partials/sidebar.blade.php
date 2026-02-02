<div class="h-100 sidebar">

    <div class="sidebar-heading border-bottom p-3 d-flex justify-content-center flex-column align-items-center">
        <img class="mb-3" src="{{ asset('favicon.ico') }}" alt="BCU Library">
        <h5 class="text-white">BCU Library</h5>
    </div>

    <div class="list-group list-group-flush">
        <a href="{{ route('dashboard') }}"
            class="list-group-item list-group-item-action text-white sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        <div class="sidebar-item">
            <a href="#"
                class="btn sidebar-btn text-white w-100 text-start rounded-0 px-3 py-2 d-flex justify-content-between align-items-center {{ request()->routeIs('books', 'copies') ? 'active' : '' }} {{ request()->routeIs('books', 'copies') ? '' : 'collapsed' }}"
                data-bs-toggle="collapse" data-bs-target="#booksDropdown"
                aria-expanded="{{ request()->routeIs('books', 'copies') ? 'true' : 'false' }}"
                aria-controls="booksDropdown">
                <span class="me-3"><i class="bi bi-book"></i> Bookshelf</span><i class="bi bi-chevron-down"></i>
            </a>

            <ul id="booksDropdown"
                class="list-unstyled collapse {{ request()->routeIs('books', 'copies') ? 'show' : '' }} ms-3 border-start border-primary ps-2 mt-1">
                <li class="sidebar-item">
                    <a href="{{ route('books') }}"
                        class="btn sidebar-btn text-white w-100 text-start rounded-0 px-3 py-1 {{ request()->routeIs('books') ? 'active' : '' }}">
                        Books
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ route('copies') }}"
                        class="btn sidebar-btn text-white w-100 text-start rounded-0 px-3 py-1 {{ request()->routeIs('copies') ? 'active' : '' }}">
                        Copies
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-item">
            <a href="#"
                class="btn sidebar-btn text-white w-100 text-start rounded-0 px-3 py-2 d-flex justify-content-between align-items-center {{ request()->routeIs('transactions.*') ? 'active' : '' }} {{ request()->routeIs('transactions.*') ? '' : 'collapsed' }}"
                data-bs-toggle="collapse" data-bs-target="#transactionsDropdown"
                aria-expanded="{{ request()->routeIs('transactions.*') ? 'true' : 'false' }}"
                aria-controls="transactionsDropdown">
                <span class="me-3"><i class="bi bi-clipboard"></i> Transactions</span><i class="bi bi-chevron-down"></i>
            </a>

            <ul id="transactionsDropdown"
                class="list-unstyled collapse {{ request()->routeIs('transactions.*') ? 'show' : '' }} ms-3 border-start border-primary ps-2 mt-1">
                <li class="sidebar-item">
                    <a href="{{ route('transactions.library') }}"
                        class="btn sidebar-btn text-white w-100 text-start rounded-0 px-3 py-1 {{ request()->routeIs('transactions.library') ? 'active' : '' }}">
                        Library
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ route('transactions.borrow') }}"
                        class="btn sidebar-btn text-white w-100 text-start rounded-0 px-3 py-1 {{ request()->routeIs('transactions.borrow') ? 'active' : '' }}">
                        Issuances
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-item">
            <a href="#"
                class="btn sidebar-btn w-100 text-start rounded-0 px-3 py-2 d-flex justify-content-between align-items-center text-white {{ request()->routeIs('faculties', 'students') ? 'active' : '' }} {{ request()->routeIs('faculties', 'students') ? '' : 'collapsed' }}"
                data-bs-toggle="collapse" data-bs-target="#usersDropdown"
                aria-expanded="{{ request()->routeIs('faculties', 'students') ? 'true' : 'false' }}"
                aria-controls="usersDropdown">
                <span class="me-3"><i class="bi bi-person"></i> Users</span><i class="bi bi-chevron-down"></i>
            </a>

            <ul id="usersDropdown"
                class="list-unstyled collapse {{ request()->routeIs('faculties', 'students') ? 'show' : '' }} ms-3 border-start border-primary ps-2 mt-1">
                <li class="sidebar-item">
                    <a href="{{ route('faculties') }}"
                        class="btn sidebar-btn text-white w-100 text-start rounded-0 px-3 py-1 {{ request()->routeIs('faculties') ? 'active' : '' }}">
                        Faculties
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('students') }}"
                        class="btn sidebar-btn text-white w-100 text-start rounded-0 px-3 py-1 {{ request()->routeIs('students') ? 'active' : '' }}">
                        Students
                    </a>
                </li>
            </ul>
        </div>

        <a href="{{ route('generate') }}"
            class="list-group-item list-group-item-action text-white sidebar-link {{ request()->routeIs('generate') ? 'active' : '' }}">
            Generate Reports
        </a>

        <a href="{{ route('archives') }}"
            class="list-group-item list-group-item-action text-white sidebar-link {{ request()->routeIs('archives') ? 'active' : '' }}">
            Archives
        </a>
    </div>
</div>
