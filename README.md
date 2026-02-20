**Home** | **[Flowchart](Flowchart.md)**

# Baguio Central University — Library Management System
### Full Application Documentation

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Technology Stack](#2-technology-stack)
3. [Prerequisites & Installation](#3-prerequisites--installation)
4. [Directory Structure](#4-directory-structure)
5. [Database Schema](#5-database-schema)
6. [Application Modules](#6-application-modules)
7. [Running the Application](#7-running-the-application)

---

## 1. Project Overview

The **Baguio Central University Library Management System (BCU-LMS)** is a desktop web application built to manage the university library's day-to-day operations. It handles book inventory, borrowing and returning of books, fines management, student and faculty records, and archiving — all within a clean, role-aware interface.

The system runs locally via **PHPDesktop**, which wraps the Laravel application in a standalone desktop executable, eliminating the need for a separate browser or web server setup for end users.

---

## 2. Technology Stack

### Backend

| Technology | Version | Purpose |
|---|---|---|
| **PHP** | ^8.4 | Core server-side language |
| **Laravel** | ^12.x | MVC application framework |
| **SQLite** | 3 | Local relational database (bundled with PHPDesktop) |

### Frontend

| Technology | Version | Purpose |
|---|---|---|
| **Livewire** | ^4.x | Full-stack reactive components without writing custom JavaScript |
| **Flux UI** | ^2.x | Blade component library built on top of Livewire for UI elements (sidebar, navs, avatars, etc.) |
| **Alpine.js** | ^3.x | Lightweight JavaScript for inline interactivity and state management |
| **Tailwind CSS** | ^3.x | Utility-first CSS framework for styling |
| **Vite** | ^5.x | Frontend asset bundler |

### Desktop Runtime

| Technology | Purpose |
|---|---|
| **PHPDesktop** | Packages the Laravel app as a native desktop application using an embedded Chromium browser and a PHP CGI server |

---

## 3. Prerequisites & Installation

### 3.1 Development Prerequisites

Ensure the following are installed on your development machine before proceeding.

#### Required Software

- **PHP 8.2+**
  Download from [https://www.php.net/downloads](https://www.php.net/downloads). Ensure the following extensions are enabled in `php.ini`:
  ```
  extension=pdo_sqlite
  extension=sqlite3
  extension=mbstring
  extension=openssl
  extension=curl
  extension=fileinfo
  extension=zip
  ```

- **Composer** (PHP dependency manager)
  Download from [https://getcomposer.org](https://getcomposer.org)

- **Node.js 20+ & npm**
  Download from [https://nodejs.org](https://nodejs.org). Required for building frontend assets with Vite.

- **Git** *(optional, for version control)*
  Download from [https://git-scm.com](https://git-scm.com)

---

### 3.2 PHPDesktop

**PHPDesktop** is a Windows tool that converts a PHP web application into a standalone desktop application. It bundles:

- An embedded **PHP CGI** server
- An embedded **Chromium** browser window
- Your application files

#### Download & Setup

1. Download the latest PHPDesktop release from the official GitHub repository:
   [https://github.com/cztomczak/phpdesktop](https://github.com/cztomczak/phpdesktop)

2. Extract the downloaded ZIP into a folder, e.g., `C:\phpdesktop\`

3. Copy your Laravel project files into the `www/` folder inside PHPDesktop's directory.

4. Edit `settings.json` in the PHPDesktop root to configure the startup URL and window settings:
   ```json
   {
     "main_window": {
       "title": "BCU Library Management System",
       "default_size": [1280, 800],
       "minimum_size": [1024, 768],
       "center_on_screen": true,
       "start_fullscreen": false
     },
     "web_server": {
       "listen_on": ["127.0.0.1", 0],
       "www_directory": "www",
       "index_files": ["index.php"],
       "cgi_interpreter": "php/php-cgi.exe",
       "cgi_extensions": ["php"]
     }
   }
   ```

5. Place the PHP binaries (`php-cgi.exe` and associated DLLs) in the `php/` folder inside the PHPDesktop directory.

6. Set the `APP_URL` in your `.env` file to `http://localhost` and `SESSION_DRIVER=file` so it works without a database-backed session server.

#### Notes for PHPDesktop Deployment

- The SQLite database file should be placed inside `www/database/` and referenced in `.env` as:
  ```
  DB_CONNECTION=sqlite
  DB_DATABASE=/full/path/to/www/database/database.sqlite
  ```
- Run `php artisan config:cache` and `php artisan route:cache` before packaging to improve load times.
- Pre-build all frontend assets with `npm run build` — Vite's dev server is not used in PHPDesktop.

---

### 3.3 Local Development Setup

```bash
# 1. Clone the repository
git clone https://github.com/your-org/bcu-lms.git
cd bcu-lms

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Copy environment file and configure
cp .env.example .env
php artisan key:generate

# 5. Set up the SQLite database
touch database/database.sqlite
php artisan migrate

# 6. Seed the database (optional)
php artisan db:seed

# 7. Build frontend assets
npm run build
# OR for development with hot reload:
npm run dev

# 8. Start the development server
php artisan serve
```

The application will be available at `http://127.0.0.1:8000`.

---

## 4. Directory Structure

```
bcu-lms/
│
├── app/
│   ├── Http/
│   │   └── Controllers/         # Standard Laravel controllers (if any)
│   ├── Livewire/                 # Livewire component classes
│   │   ├── Books/
│   │   │   ├── BookList.php
│   │   │   ├── BookForm.php
│   │   │   └── CopyList.php
│   │   ├── Transactions/
│   │   │   ├── Issuance.php
│   │   │   └── LibraryTransaction.php
│   │   ├── Users/
│   │   │   ├── StudentList.php
│   │   │   └── FacultyList.php
│   │   ├── Fines/
│   │   │   ├── StudentFines.php
│   │   │   └── FacultyFines.php
│   │   ├── Archives/
│   │   │   ├── ArchiveBooks.php
│   │   │   ├── ArchiveTransactions.php
│   │   │   └── ArchiveUsers.php
│   │   ├── Dashboard.php
│   │   └── Reports.php
│   ├── Models/
│   │   ├── Book.php
│   │   ├── Copy.php
│   │   ├── Course.php
│   │   ├── Department.php
│   │   ├── Faculty.php
│   │   ├── FacultyBorrow.php
│   │   ├── FacultyFine.php
│   │   ├── LibraryTransaction.php
│   │   ├── Student.php
│   │   ├── StudentBorrow.php
│   │   ├── StudentFine.php
│   │   ├── TransactionArchive.php
│   │   └── ArchivesLibrary.php
│   └── Providers/
│       └── AppServiceProvider.php
│
├── database/
│   ├── migrations/
│   │   ├── xxxx_xx_xx_create_cache_table.php
│   │   ├── xxxx_xx_xx_create_jobs_table.php
│   │   ├── xxxx_xx_xx_create_departments_table.php
│   │   ├── xxxx_xx_xx_create_books_table.php
│   │   ├── xxxx_xx_xx_create_courses_table.php
│   │   ├── xxxx_xx_xx_create_copies_table.php
│   │   ├── xxxx_xx_xx_create_archives_library_table.php
│   │   ├── xxxx_xx_xx_create_faculties_table.php
│   │   ├── xxxx_xx_xx_create_students_table.php
│   │   ├── xxxx_xx_xx_create_faculty_borrows_table.php
│   │   ├── xxxx_xx_xx_create_student_borrows_table.php
│   │   ├── xxxx_xx_xx_create_library_transactions_table.php
│   │   ├── xxxx_xx_xx_create_student_fines_table.php
│   │   ├── xxxx_xx_xx_create_faculty_fines_table.php
│   │   ├── xxxx_xx_xx_create_transaction_archives_table.php
│   │   └── xxxx_xx_xx_create_sessions_table.php
│   ├── seeders/
│   │   └── DatabaseSeeder.php
│   └── database.sqlite              # SQLite database file (auto-generated)
│
├── resources/
│   ├── css/
│   │   └── app.css                  # Tailwind CSS entry point
│   ├── js/
│   │   └── app.js                   # Alpine.js / JS entry point
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php        # Main layout shell
│       │   └── partial/
│       │       ├── header.blade.php # HTML head, Vite assets, Livewire styles
│       │       ├── sidebar.blade.php# Flux sidebar navigation
│       │       └── footer.blade.php # Livewire scripts, closing tags
│       ├── livewire/
│       │   ├── books/
│       │   │   ├── book-list.blade.php
│       │   │   ├── book-form.blade.php
│       │   │   └── copy-list.blade.php
│       │   ├── transactions/
│       │   │   ├── issuance.blade.php
│       │   │   └── library-transaction.blade.php
│       │   ├── users/
│       │   │   ├── student-list.blade.php
│       │   │   └── faculty-list.blade.php
│       │   ├── fines/
│       │   │   ├── student-fines.blade.php
│       │   │   └── faculty-fines.blade.php
│       │   ├── archives/
│       │   │   ├── archive-books.blade.php
│       │   │   ├── archive-transactions.blade.php
│       │   │   └── archive-users.blade.php
│       │   ├── dashboard.blade.php
│       │   └── reports.blade.php
│       └── components/              # Reusable Blade/Flux components
│
├── routes/
│   └── web.php                      # Application routes
│
├── config/
│   ├── app.php
│   ├── database.php
│   └── ...
│
├── public/
│   ├── index.php                    # Application entry point
│   ├── favicon.ico                  # BCU logo / favicon
│   └── build/                       # Compiled Vite assets (after npm run build)
│
├── storage/
│   ├── app/
│   ├── framework/
│   └── logs/
│
├── bootstrap/
│   └── app.php
│
├── .env                             # Environment configuration
├── .env.example
├── artisan                          # Laravel CLI
├── composer.json
├── package.json
├── tailwind.config.js
├── vite.config.js
└── README.md
```

---

## 5. Database Schema

The application uses **SQLite** as its database engine. All tables are defined via Laravel migrations. Foreign key constraints are enforced via `PRAGMA foreign_keys=ON`.

### Entity Overview

#### `departments`
Stores academic departments. Acts as the root entity for both books and users.

| Column | Type | Notes |
|---|---|---|
| `department_code` | TEXT | Primary Key |
| `name` | TEXT | Indexed |

---

#### `courses`
Academic courses, each belonging to a department.

| Column | Type | Notes |
|---|---|---|
| `course_code` | TEXT | Primary Key |
| `department_id` | TEXT | FK → `departments.department_code` |
| `name` | TEXT | Indexed |

---

#### `books`
The master book catalog.

| Column | Type | Notes |
|---|---|---|
| `id` | INTEGER | Auto-increment PK |
| `title` | TEXT | Indexed |
| `author` | TEXT | |
| `publication_date` | DATE | |
| `publisher` | TEXT | |
| `isbn` | TEXT | |
| `department_id` | TEXT | FK → `departments.department_code` |
| `category` | TEXT | |
| `copies` | INTEGER | Total copy count |

---

#### `copies`
Individual physical copies of a book, linked to a specific course.

| Column | Type | Notes |
|---|---|---|
| `copy_id` | TEXT | Primary Key |
| `book_id` | INTEGER | FK → `books.id` |
| `course_id` | TEXT | FK → `courses.course_code` |
| `status` | TEXT | Default: `Available` |
| `condition` | TEXT | Default: `Good` |

---

#### `students`

| Column | Type | Notes |
|---|---|---|
| `student_id` | TEXT | Primary Key |
| `first_name` | TEXT | |
| `middle_name` | TEXT | |
| `last_name` | TEXT | Composite index with names |
| `department_id` | TEXT | FK → `departments.department_code` |
| `course_id` | TEXT | FK → `courses.course_code` |
| `year_level` | INTEGER | |

---

#### `faculties`

| Column | Type | Notes |
|---|---|---|
| `faculty_id` | TEXT | Primary Key |
| `first_name` | TEXT | |
| `middle_name` | TEXT | |
| `last_name` | TEXT | Composite index with names |
| `department_id` | TEXT | FK → `departments.department_code` |
| `occupation` | TEXT | |

---

#### `student_borrows` / `faculty_borrows`
Tracks borrowing transactions for students and faculty respectively.

| Column | Type | Notes |
|---|---|---|
| `id` | INTEGER | Auto-increment PK |
| `student_id` / `faculty_id` | TEXT | FK to respective table |
| `copy_id` | TEXT | FK → `copies.copy_id` |
| `ref_number` | TEXT | Transaction reference |
| `date_borrowed` | DATE | |
| `due_date` | DATE | |
| `date_returned` | DATE | |

---

#### `student_fines` / `faculty_fines`
Tracks overdue or damage fines.

| Column | Type | Notes |
|---|---|---|
| `id` | INTEGER | Auto-increment PK |
| `student_id` / `faculty_id` | TEXT | FK to respective table |
| `copy_id` | TEXT | FK → `copies.copy_id` |
| `amount` | TEXT | Fine amount |
| `reason` | TEXT | |
| `status` | INTEGER | `0` = Unpaid, `1` = Paid |
| `date_paid` | DATE | Nullable |

---

#### `library_transactions`
General library-level transaction log (e.g., in-library use, walk-ins).

| Column | Type | Notes |
|---|---|---|
| `id` | INTEGER | Auto-increment PK |
| `transaction_name` | TEXT | |
| `ref_number` | TEXT | |

---

#### `archives_library`
Archived/retired book records removed from active inventory.

| Column | Type | Notes |
|---|---|---|
| `id` | INTEGER | Auto-increment PK |
| `book_id` | INTEGER | Original book reference |
| `title`, `author`, `publisher`, etc. | TEXT | Snapshot at time of archiving |
| `department_id` | TEXT | FK → `departments.department_code` |

---

#### `transaction_archives`
Links archived student/faculty borrow or library transaction records.

| Column | Type | Notes |
|---|---|---|
| `id` | INTEGER | Auto-increment PK |
| `student_borrow_transaction_id` | TEXT | Nullable |
| `faculty_borrow_transaction_id` | TEXT | Nullable |
| `library_transaction_id` | TEXT | Nullable |
| `name` | TEXT | |

---

## 6. Application Modules

The sidebar navigation defines the primary modules of the application:

### Dashboard
An at-a-glance summary of library activity: total books, active borrows, pending fines, and recent transactions.

### Bookshelf
- **Books** — Add, edit, search, and manage the master book catalog. Each book is tied to a department and has a total copy count.
- **Copies** — Manage individual physical copies of books. Each copy has a status (`Available`, `Borrowed`, `Lost`, etc.) and a condition rating.

### Transactions
- **Issuance** — Process book borrowing and returning for both students and faculty. Generates a reference number per transaction.
- **Library** — Log in-library usage or reading room transactions that don't involve a take-home borrow.

### Users
- **Students** — Add and manage student profiles, including department and course enrollment.
- **Faculties** — Add and manage faculty profiles and departmental assignments.

### Fines
- **Students** — View and manage outstanding fines for student borrowers. Mark fines as paid and record payment date.
- **Faculties** — Same as above for faculty borrowers.

### Archives
- **Books** — View books that have been retired from active inventory.
- **Transactions** — Historical log of completed or archived borrow transactions.
- **Users** — Archived student or faculty profiles no longer actively using the library.

### Reports
Generate and print library reports, covering borrow frequency, overdue items, fine collection summaries, and department-level statistics.

---

## 7. Running the Application

### Development Mode

```bash
# Terminal 1 — Start the Laravel dev server
php artisan serve

# Terminal 2 — Start Vite for hot module reloading
npm run dev
```

Visit `http://127.0.0.1:8000` in your browser.

### Production / PHPDesktop Mode

```bash
# Build optimized frontend assets
npm run build

# Cache config and routes for faster startup
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Then launch `phpdesktop-chrome.exe` from the PHPDesktop directory. The app will open in a self-contained desktop window — no browser installation required by end users.

### Useful Artisan Commands

| Command | Purpose |
|---|---|
| `php artisan migrate` | Run all pending database migrations |
| `php artisan migrate:fresh` | Drop all tables and re-run migrations |
| `php artisan migrate:fresh --seed` | Fresh migration with seeders |
| `php artisan db:seed` | Run database seeders |
| `php artisan config:cache` | Cache configuration for production |
| `php artisan route:cache` | Cache routes for production |
| `php artisan view:cache` | Pre-compile Blade views |
| `php artisan cache:clear` | Clear application cache |
| `php artisan optimize:clear` | Clear all cached files |
