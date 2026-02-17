# Baguio Central University - Library Management System Project Documentation

### Project Structure
**Application Executable**
```
BCULMS.exe
  └── www/
  ┣ app
  ┃ ┣ Console
  ┃ ┃ ┣ Commands
  ┃ ┃ ┃ ┗ ProcessDailyOverduePenalties.php
  ┃ ┃ ┗ Kernel.php
```
**Laravel Controllers**
```
  ┃ ┣ Http
  ┃ ┃ ┗ Controllers
  ┃ ┃   ┣ ArchiveLibraryController.php
  ┃ ┃   ┣ ArchiveTransactionsController.php
  ┃ ┃   ┣ ArchiveUsersController.php
  ┃ ┃   ┣ BookController.php
  ┃ ┃   ┣ CopyController.php
  ┃ ┃   ┣ CourseController.php
  ┃ ┃   ┣ DashboardController.php
  ┃ ┃   ┣ DepartmentController.php
  ┃ ┃   ┣ FacultyController.php
  ┃ ┃   ┣ FinesFacultyController.php
  ┃ ┃   ┣ FinesStudentController.php
  ┃ ┃   ┣ ReportsController.php
  ┃ ┃   ┣ StudentController.php
  ┃ ┃   ┣ TransactionLibraryController.php
  ┃ ┃   ┗ TransactionUsersController.php
  ```
**Livewire Components for Reactivity**
  ```
  ┃ ┣ Livewire
  ┃ ┃ ┣ Components
  ┃ ┃ ┣ Pages
  ┃ ┃ ┃ ┣ Archives
  ┃ ┃ ┃ ┃ ┣ Libraries.php
  ┃ ┃ ┃ ┃ ┣ Transaction.php
  ┃ ┃ ┃ ┃ ┗ Users.php
  ┃ ┃ ┃ ┣ Books
  ┃ ┃ ┃ ┃ ┣ BookCreate.php
  ┃ ┃ ┃ ┃ ┣ BookEdit.php
  ┃ ┃ ┃ ┃ ┗ BookIndex.php
  ┃ ┃ ┃ ┣ Copies
  ┃ ┃ ┃ ┃ ┣ CopyIndex.php
  ┃ ┃ ┃ ┃ ┗ CopyShow.php
  ┃ ┃ ┃ ┣ Fines
  ┃ ┃ ┃ ┃ ┣ Faculty.php
  ┃ ┃ ┃ ┃ ┗ Student.php
  ┃ ┃ ┃ ┣ Reports
  ┃ ┃ ┃ ┃ ┣ Generate.php
  ┃ ┃ ┃ ┃ ┗ Index.php
  ┃ ┃ ┃ ┣ Transactions
  ┃ ┃ ┃ ┃ ┣ Issuance.php
  ┃ ┃ ┃ ┃ ┗ Library.php
  ┃ ┃ ┃ ┗ Users
  ┃ ┃ ┃   ┣ FacultyCreate.php
  ┃ ┃ ┃   ┣ FacultyEdit.php
  ┃ ┃ ┃   ┣ FacultyIndex.php
  ┃ ┃ ┃   ┣ StudentCreate.php
  ┃ ┃ ┃   ┣ StudentEdit.php
  ┃ ┃ ┃   ┗ StudentIndex.php
  ┃ ┃ ┗ Dashboard.php
```
**Models for Relationships and Database Rules**
```
  ┃ ┣ Models
  ┃ ┃ ┣ ArchivesLibrary.php
  ┃ ┃ ┣ Book.php
  ┃ ┃ ┣ Copy.php
  ┃ ┃ ┣ Course.php
  ┃ ┃ ┣ Department.php
  ┃ ┃ ┣ Faculty.php
  ┃ ┃ ┣ FacultyArchive.php
  ┃ ┃ ┣ FacultyBorrow.php
  ┃ ┃ ┣ FacultyFine.php
  ┃ ┃ ┣ FacultyFineArchive.php
  ┃ ┃ ┣ LibraryTransaction.php
  ┃ ┃ ┣ Report.php
  ┃ ┃ ┣ Student.php
  ┃ ┃ ┣ StudentArchive.php
  ┃ ┃ ┣ StudentBorrow.php
  ┃ ┃ ┣ StudentFine.php
  ┃ ┃ ┣ StudentFineArchive.php
  ┃ ┃ ┗ TransactionArchive.php
```
**Providers for Registering Application Evenets**
```
  ┃ ┣ Providers
  ┃ ┃ ┗ AppServiceProvider.php
```
**Custom Business Logic of the Application**
```
  ┃ ┗ Services
  ┃   ┣ ArchiveTransactionService.php
  ┃   ┗ AutomaticPenaltyService.php
  ┣ bootstrap
  ┃ ┣ cache
  ┃ ┃ ┣ .gitignore
  ┃ ┃ ┣ packages.php
  ┃ ┃ ┗ services.php
  ┃ ┣ app.php
  ┃ ┗ providers.php
  ┣ config
  ┃ ┣ app.php
  ┃ ┣ auth.php
  ┃ ┣ cache.php
  ┃ ┣ database.php
  ┃ ┣ filesystems.php
  ┃ ┣ library.php
  ┃ ┣ livewire.php
  ┃ ┣ logging.php
  ┃ ┣ mail.php
  ┃ ┣ queue.php
  ┃ ┣ services.php
  ┃ ┗ session.php
  ┣ database
  ┃ ┣ factories
  ┃ ┃ ┗ UserFactory.php
  ┃ ┣ migrations
  ┃ ┃ ┣ 0001_01_01_000001_create_cache_table.php
  ┃ ┃ ┣ 0001_01_01_000002_create_jobs_table.php
  ┃ ┃ ┣ 2026_02_02_090342_base_table.php
  ┃ ┃ ┣ 2026_02_02_090356_books_table.php
  ┃ ┃ ┣ 2026_02_02_090404_courses.php
  ┃ ┃ ┣ 2026_02_02_090405_copies_table.php
  ┃ ┃ ┣ 2026_02_02_090423_archives_library.php
  ┃ ┃ ┣ 2026_02_02_090428_faculties.php
  ┃ ┃ ┣ 2026_02_02_090508_students.php
  ┃ ┃ ┣ 2026_02_02_090538_transactions_borrow_faculty.php
  ┃ ┃ ┣ 2026_02_02_090538_transactions_borrow_student.php
  ┃ ┃ ┣ 2026_02_02_090547_transactions_library.php
  ┃ ┃ ┣ 2026_02_02_090555_fines_students.php
  ┃ ┃ ┣ 2026_02_02_101611_archive_transactions.php
  ┃ ┃ ┣ 2026_02_02_101629_fines_faculties.php
  ┃ ┃ ┣ 2026_02_02_134921_create_sessions_table.php
  ┃ ┃ ┣ 2026_02_17_103126_student_fines_archive.php
  ┃ ┃ ┣ 2026_02_17_103136_faculty_fines_archive.php
  ┃ ┃ ┣ 2026_02_17_103320_students_archive.php
  ┃ ┃ ┣ 2026_02_17_103328_faculties_archive.php
  ┃ ┃ ┗ 2026_02_17_180118_reports.php
  ┃ ┣ seeders
  ┃ ┃ ┣ CourseSeeder.php
  ┃ ┃ ┣ DatabaseSeeder.php
  ┃ ┃ ┗ DepartmentSeeder.php
  ┃ ┣ .gitignore
  ┃ ┗ database.sqlite
  ┣ public
  ┃ ┣ build
  ┃ ┃ ┣ assets
  ┃ ┃ ┃ ┣ app-Bz1z3Khb.css
  ┃ ┃ ┃ ┗ app-CY4WpChI.js
  ┃ ┃ ┗ manifest.json
  ┃ ┣ fonts
  ┃ ┃ ┣ Montserrat-Italic-VariableFont_wght.ttf
  ┃ ┃ ┗ Montserrat-VariableFont_wght.ttf
  ┃ ┣ .htaccess
  ┃ ┣ favicon.ico
  ┃ ┣ index.php
  ┃ ┗ robots.txt
  ┣ resources
  ┃ ┣ css
  ┃ ┃ ┗ app.css
  ┃ ┣ js
  ┃ ┃ ┗ app.js
  ┃ ┗ views
  ┃   ┣ components
  ┃   ┣ layouts
  ┃   ┃ ┗ app.blade.php
  ┃   ┣ livewire
  ┃   ┃ ┣ components
  ┃   ┃ ┃ ┗ pagination.blade.php
  ┃   ┃ ┣ pages
  ┃   ┃ ┃ ┣ archives
  ┃   ┃ ┃ ┃ ┣ libraries.blade.php
  ┃   ┃ ┃ ┃ ┣ transaction.blade.php
  ┃   ┃ ┃ ┃ ┗ users.blade.php
  ┃   ┃ ┃ ┣ books
  ┃   ┃ ┃ ┃ ┣ book-create.blade.php
  ┃   ┃ ┃ ┃ ┣ book-edit.blade.php
  ┃   ┃ ┃ ┃ ┗ book-index.blade.php
  ┃   ┃ ┃ ┣ copies
  ┃   ┃ ┃ ┃ ┣ copy-index.blade.php
  ┃   ┃ ┃ ┃ ┗ copy-show.blade.php
  ┃   ┃ ┃ ┣ fines
  ┃   ┃ ┃ ┃ ┣ faculty.blade.php
  ┃   ┃ ┃ ┃ ┗ student.blade.php
  ┃   ┃ ┃ ┣ reports
  ┃   ┃ ┃ ┃ ┣ generate.blade.php
  ┃   ┃ ┃ ┃ ┗ index.blade.php
  ┃   ┃ ┃ ┣ transactions
  ┃   ┃ ┃ ┃ ┣ issuance.blade.php
  ┃   ┃ ┃ ┃ ┗ library.blade.php
  ┃   ┃ ┃ ┗ users
  ┃   ┃ ┃   ┣ faculty-create.blade.php
  ┃   ┃ ┃   ┣ faculty-edit.blade.php
  ┃   ┃ ┃   ┣ faculty-index.blade.php
  ┃   ┃ ┃   ┣ student-create.blade.php
  ┃   ┃ ┃   ┣ student-edit.blade.php
  ┃   ┃ ┃   ┗ student-index.blade.php
  ┃   ┃ ┗ dashboard.blade.php
  ┃   ┗ partial
  ┃     ┣ footer.blade.php
  ┃     ┣ header.blade.php
  ┃     ┗ sidebar.blade.php
  ┗ routes
    ┣ console.php
    ┗ web.php

```
