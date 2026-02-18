**[Home](README.md)** | **[Flowchart]**

# BCU Library Management System — Application Flowchart

> Renders in GitHub, GitLab, Obsidian, VS Code (with Mermaid extension), and most modern documentation platforms.

---

## System Launch

```mermaid
flowchart TD
    A([🖥️ Launch PHPDesktop]) --> B[Embedded PHP-CGI Server Starts]
    B --> C[Chromium Window Opens]
    C --> D[📊 Dashboard]
    D --> E{Navigate via Sidebar}
    E --> F[📚 Bookshelf]
    E --> G[🔄 Transactions]
    E --> H[👥 Users]
    E --> I[💰 Fines]
    E --> J[🗄️ Archives]
    E --> K[📋 Reports]
```

---

## 📚 Bookshelf — Books & Copies

```mermaid
flowchart TD
    A([📚 Books Module]) --> B{Action}
    B -->|Add| C[Fill in Title, Author,\nISBN, Dept, Category]
    B -->|Search/Edit| D[Filter by Title,\nDept or Category]
    C --> E[Save Book to Database]
    D --> E

    E --> F([📄 Copies Module])
    F --> G[Add Copy\nassign copy_id, course,\ncondition]
    G --> H{Copy Status?}
    H -->|Available| I([✅ Ready for Issuance])
    H -->|Borrowed| J([⚠️ Mark as Borrowed])
    H -->|Lost / Damaged| K([❌ Flag for Review])
```

---

## 👥 Users — Students & Faculties

```mermaid
flowchart TD
    A{User Type} --> B([🎓 Students])
    A --> C([👨‍🏫 Faculties])

    B --> D[Add Student\nID, Name, Dept, Course, Year Level]
    C --> E[Add Faculty\nID, Name, Dept, Occupation]

    D --> F[Profile Saved to DB]
    E --> F

    F --> G([✅ Borrower Eligible for Issuance])
```

---

## 🔄 Transactions — Issuance & Return

```mermaid
flowchart TD
    A([🔄 Issuance Module]) --> B{Borrower Type?}
    B -->|Student| C[Select Student Record]
    B -->|Faculty| D[Select Faculty Record]

    C --> E[Select Book Copy by copy_id]
    D --> E

    E --> F{Copy Available?}
    F -->|No| G([❌ Show Unavailable Error])
    F -->|Yes| H[Set Borrow Date & Due Date]

    H --> I[Generate Reference Number]
    I --> J[Save to student_borrows\nor faculty_borrows]
    J --> K[Update Copy Status → Borrowed]
    K --> L([✅ Issuance Complete])

    L --> M([📦 Return Process])
    M --> N[Borrower Returns Copy]
    N --> O[Record date_returned]
    O --> P{Returned On Time?}

    P -->|Yes| Q[Update Copy Status → Available]
    Q --> R([✅ Transaction Closed])

    P -->|No| S[Calculate Fine Amount]
    S --> T[Create Fine Record\nstudent_fines or faculty_fines]
    T --> U[Update Copy Status → Available]
    U --> V([⚠️ Fine Pending Payment])
```

---

## 🏛️ Transactions — Library Usage

```mermaid
flowchart TD
    A([🏛️ Library Module]) --> B[Log In-Library Usage\nWalk-ins, Reading Room]
    B --> C[Enter Transaction Name\n& Reference Number]
    C --> D[Save to library_transactions]
    D --> E([✅ Transaction Logged])
```

---

## 💰 Fines — Students & Faculties

```mermaid
flowchart TD
    A{Fine Type} --> B([💰 Student Fines])
    A --> C([💰 Faculty Fines])

    B --> D[View All Fines\nFilter: Unpaid / Paid]
    C --> D

    D --> E{Fine Status?}
    E -->|Unpaid| F[status = 0\nAwaiting Payment]
    E -->|Paid| G[Mark as Paid\nRecord date_paid]

    F --> H{Payment Received?}
    H -->|Yes| G
    H -->|No| F

    G --> I([✅ Fine Record Updated])
```

---

## 🗄️ Archives

```mermaid
flowchart TD
    A{Archive Type} --> B([📖 Books])
    A --> C([🔄 Transactions])
    A --> D([👥 Users])

    B --> E[Retire Book from Active Catalog\nSnapshot data at archive time]
    C --> F[Archive Borrow / Library\nTransaction Records]
    D --> G[Archive Inactive\nStudent or Faculty Profile]

    E --> H[Save to archives_library]
    F --> I[Save to transaction_archives]
    G --> J[Remove from Active User Lists]

    H --> K([✅ Record Archived])
    I --> K
    J --> K
```

---

## 📋 Reports

```mermaid
flowchart TD
    A([📋 Reports Module]) --> B[Select Report Type\nBorrows · Fines · Overdue · Dept Stats]
    B --> C[Set Filters & Date Range]
    C --> D[Query Database via Eloquent ORM]
    D --> E[Compile Results]
    E --> F([✅ Display Print-Ready Report])
```

---

## Technology Stack

| Layer | Technology |
|---|---|
| Language | PHP 8.2+ |
| Framework | Laravel 11 |
| Reactivity | Livewire 3 |
| UI Components | Flux UI |
| JS Interactivity | Alpine.js |
| Styling | Tailwind CSS |
| Asset Bundler | Vite |
| Database | SQLite |
| Desktop Runtime | PHPDesktop |

---

*Baguio Central University — Library Management System · February 2026*
