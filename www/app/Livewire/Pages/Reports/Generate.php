<?php

namespace App\Livewire\Pages\Reports;

use App\Models\Report;
use App\Models\StudentBorrow;
use App\Models\FacultyBorrow;
use App\Models\Book;
use App\Models\ArchivesLibrary;
use App\Models\StudentFine;
use App\Models\FacultyFine;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Carbon\Carbon;

use Livewire\Attributes\Lazy;

#[Lazy] class Generate extends Component
{
    // ── Form state ────────────────────────────────────────────────────────────

    public bool $showModal = false;

    public string $reportType  = '';
    public string $preset      = 'monthly';

    // For presets that need a year
    public int $year;

    // For quarterly (1-4) and semi-annual (1-2)
    public ?int $quarter = null;

    // For monthly (1-12)
    public ?int $month = null;

    // Custom range
    public string $customFrom = '';
    public string $customTo   = '';

    // Derived / computed
    public string $resolvedFrom = '';
    public string $resolvedTo   = '';

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->year    = now()->year;
        $this->month   = now()->month;
        $this->quarter = (int) ceil(now()->month / 3);
    }

    // ── Watchers ──────────────────────────────────────────────────────────────

    public function updatedPreset(): void
    {
        $this->resolveRange();
    }

    public function updatedYear(): void
    {
        $this->resolveRange();
    }

    public function updatedQuarter(): void
    {
        $this->resolveRange();
    }

    public function updatedMonth(): void
    {
        $this->resolveRange();
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function open(): void
    {
        $this->reset(['reportType', 'customFrom', 'customTo', 'resolvedFrom', 'resolvedTo']);
        $this->preset  = 'monthly';
        $this->year    = now()->year;
        $this->month   = now()->month;
        $this->quarter = (int) ceil(now()->month / 3);
        $this->resolveRange();
        $this->showModal = true;
    }

    public function close(): void
    {
        $this->showModal = false;
    }

    public function generate(): void
    {
        $this->validate([
            'reportType'  => 'required|in:' . implode(',', array_keys(Report::TYPES)),
            'preset'      => 'required|in:' . implode(',', array_keys(Report::PRESETS)),
            'customFrom'  => 'required_if:preset,custom|nullable|date',
            'customTo'    => 'required_if:preset,custom|nullable|date|after_or_equal:customFrom',
        ]);

        $this->resolveRange();

        $dateFrom = Carbon::parse($this->resolvedFrom)->startOfDay();
        $dateTo   = Carbon::parse($this->resolvedTo)->endOfDay();

        [$data, $total] = $this->collectData($dateFrom, $dateTo);

        $title = $this->buildTitle();

        Report::create([
            'title'        => $title,
            'report_type'  => $this->reportType,
            'period_preset' => $this->preset,
            'date_from'    => $dateFrom->toDateString(),
            'date_to'      => $dateTo->toDateString(),
            'report_data'  => $data,
            'total_records' => $total,
        ]);

        $this->close();
        $this->dispatch('report-generated');
        session()->flash('success', "Report \"{$title}\" generated successfully.");
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function resolveRange(): void
    {
        if ($this->preset === 'custom') {
            $this->resolvedFrom = $this->customFrom;
            $this->resolvedTo   = $this->customTo;
            return;
        }

        $dates = Report::datesForPreset(
            $this->preset,
            $this->year,
            $this->quarter,
            $this->month,
        );

        $this->resolvedFrom = $dates['date_from'];
        $this->resolvedTo   = $dates['date_to'];
    }

    private function buildTitle(): string
    {
        $typeLabel = Report::TYPES[$this->reportType];

        if ($this->preset === 'custom') {
            return "{$typeLabel} ({$this->resolvedFrom} – {$this->resolvedTo})";
        }

        $presetLabel = Report::PRESETS[$this->preset];

        return match ($this->preset) {
            'annual'      => "{$typeLabel} – Annual {$this->year}",
            'semi_annual' => "{$typeLabel} – " . ($this->quarter === 1 ? 'H1' : 'H2') . " {$this->year}",
            'quarterly'   => "{$typeLabel} – Q{$this->quarter} {$this->year}",
            'monthly'     => "{$typeLabel} – " . Carbon::create($this->year, $this->month)->format('F Y'),
            default       => "{$typeLabel} ({$presetLabel})",
        };
    }

    /**
     * Pull rows from the database based on report type and date range.
     *
     * @return array{0: array, 1: int}  [rows, count]
     */
    private function collectData(Carbon $from, Carbon $to): array
    {
        $data = [];

        switch ($this->reportType) {
            case 'issuance':
                $studentRows = DB::table('student_borrows')
                    ->join('students', 'student_borrows.student_id', '=', 'students.student_id')
                    ->join('copies',   'student_borrows.copy_id',    '=', 'copies.copy_id')
                    ->join('books',    'copies.book_id',             '=', 'books.id')
                    ->whereBetween('student_borrows.date_borrowed', [$from, $to])
                    ->select(
                        'student_borrows.ref_number',
                        DB::raw("'Student' as borrower_type"),
                        DB::raw("students.first_name || ' ' || students.last_name as borrower_name"),
                        'books.title as book_title',
                        'student_borrows.date_borrowed',
                        'student_borrows.due_date',
                        'student_borrows.date_returned',
                    )
                    ->get()->toArray();

                $facultyRows = DB::table('faculty_borrows')
                    ->join('faculties', 'faculty_borrows.faculty_id', '=', 'faculties.faculty_id')
                    ->join('copies',    'faculty_borrows.copy_id',    '=', 'copies.copy_id')
                    ->join('books',     'copies.book_id',             '=', 'books.id')
                    ->whereBetween('faculty_borrows.date_borrowed', [$from, $to])
                    ->select(
                        'faculty_borrows.ref_number',
                        DB::raw("'Faculty' as borrower_type"),
                        DB::raw("faculties.first_name || ' ' || faculties.last_name as borrower_name"),
                        'books.title as book_title',
                        'faculty_borrows.date_borrowed',
                        'faculty_borrows.due_date',
                        'faculty_borrows.date_returned',
                    )
                    ->get()->toArray();

                $data  = array_merge($studentRows, $facultyRows);
                break;

            case 'book_added':
                $data = DB::table('books')
                    ->whereBetween('created_at', [$from, $to])
                    ->select('id', 'title', 'author', 'isbn', 'publisher', 'copies', 'created_at')
                    ->get()->toArray();
                break;

            case 'book_archived':
                $data = DB::table('archives_library')
                    ->whereBetween('created_at', [$from, $to])
                    ->select('id', 'title', 'author', 'isbn', 'publisher', 'copies', 'created_at')
                    ->get()->toArray();
                break;

            case 'fines_student':
                $data = DB::table('student_fines')
                    ->join('students', 'student_fines.student_id', '=', 'students.student_id')
                    ->join('copies',   'student_fines.copy_id',    '=', 'copies.copy_id')
                    ->join('books',    'copies.book_id',           '=', 'books.id')
                    ->whereBetween('student_fines.created_at', [$from, $to])
                    ->select(
                        'student_fines.id',
                        DB::raw("students.first_name || ' ' || students.last_name as name"),
                        'books.title as book_title',
                        'student_fines.amount',
                        'student_fines.reason',
                        'student_fines.status',
                        'student_fines.date_paid',
                    )
                    ->get()->toArray();
                break;

            case 'fines_faculty':
                $data = DB::table('faculty_fines')
                    ->join('faculties', 'faculty_fines.faculty_id', '=', 'faculties.faculty_id')
                    ->join('copies',    'faculty_fines.copy_id',    '=', 'copies.copy_id')
                    ->join('books',     'copies.book_id',           '=', 'books.id')
                    ->whereBetween('faculty_fines.created_at', [$from, $to])
                    ->select(
                        'faculty_fines.id',
                        DB::raw("faculties.first_name || ' ' || faculties.last_name as name"),
                        'books.title as book_title',
                        'faculty_fines.amount',
                        'faculty_fines.reason',
                        'faculty_fines.status',
                        'faculty_fines.date_paid',
                    )
                    ->get()->toArray();
                break;

            case 'fines_both':
                $studentFines = DB::table('student_fines')
                    ->join('students', 'student_fines.student_id', '=', 'students.student_id')
                    ->join('copies',   'student_fines.copy_id',    '=', 'copies.copy_id')
                    ->join('books',    'copies.book_id',           '=', 'books.id')
                    ->whereBetween('student_fines.created_at', [$from, $to])
                    ->select(
                        'student_fines.id',
                        DB::raw("'Student' as borrower_type"),
                        DB::raw("students.first_name || ' ' || students.last_name as name"),
                        'books.title as book_title',
                        'student_fines.amount',
                        'student_fines.reason',
                        'student_fines.status',
                        'student_fines.date_paid',
                    )
                    ->get()->toArray();

                $facultyFines = DB::table('faculty_fines')
                    ->join('faculties', 'faculty_fines.faculty_id', '=', 'faculties.faculty_id')
                    ->join('copies',    'faculty_fines.copy_id',    '=', 'copies.copy_id')
                    ->join('books',     'copies.book_id',           '=', 'books.id')
                    ->whereBetween('faculty_fines.created_at', [$from, $to])
                    ->select(
                        'faculty_fines.id',
                        DB::raw("'Faculty' as borrower_type"),
                        DB::raw("faculties.first_name || ' ' || faculties.last_name as name"),
                        'books.title as book_title',
                        'faculty_fines.amount',
                        'faculty_fines.reason',
                        'faculty_fines.status',
                        'faculty_fines.date_paid',
                    )
                    ->get()->toArray();

                $data = array_merge($studentFines, $facultyFines);
                break;
        }

        return [$data, count($data)];
    }

    // ── Render ────────────────────────────────────────────────────────────────

    // ── Computed Properties ───────────────────────────────────────────────────

    public function getPreviewUrlProperty(): string
    {
        if (!$this->reportType || !$this->resolvedFrom || !$this->resolvedTo) {
            return '#';
        }

        return route('reports.preview', [
            'type'   => $this->reportType,
            'preset' => $this->preset,
            'from'   => $this->resolvedFrom,
            'to'     => $this->resolvedTo,
        ]);
    }

    // ── Render ────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.pages.reports.generate', [
            'reportTypes' => Report::TYPES,
            'presets'     => Report::PRESETS,
            'years'       => range(now()->year, now()->year - 5),
            'months'      => array_combine(range(1, 12), array_map(
                fn($m) => Carbon::create(null, $m)->format('F'),
                range(1, 12)
            )),
            'subGridClass' => match ($this->preset) {
                'monthly'     => 'grid-cols-2',
                'quarterly'   => 'grid-cols-3',
                'semi_annual' => 'grid-cols-3',
                default       => 'grid-cols-1',
            },
        ]);
    }
}
