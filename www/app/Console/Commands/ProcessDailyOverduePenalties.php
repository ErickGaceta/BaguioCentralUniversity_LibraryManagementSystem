<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentBorrow;
use App\Models\FacultyBorrow;
use App\Models\StudentFine;
use App\Models\FacultyFine;
use Carbon\Carbon;

class ProcessDailyOverduePenalties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'library:process-daily-penalties
                            {--dry-run : Run without creating fines}
                            {--amount= : Daily penalty amount (default: 20.00)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process daily ₱20 overdue penalties for each overdue book (runs at 10 AM daily)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🕐 Processing daily overdue penalties...');
        $this->info('⏰ Scheduled for: 10:00 AM daily');

        $dryRun = $this->option('dry-run');
        $dailyPenalty = $this->option('amount') ?? config('library.daily_overdue_penalty', 20.00);
        $today = Carbon::today()->format('Y-m-d');

        $this->info("💰 Daily penalty: ₱{$dailyPenalty}");

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No fines will be created');
        }

        // Process student overdue penalties
        $studentPenalties = $this->processStudentPenalties($dailyPenalty, $today, $dryRun);

        // Process faculty overdue penalties
        $facultyPenalties = $this->processFacultyPenalties($dailyPenalty, $today, $dryRun);

        // Summary
        $this->newLine();
        $this->info('📊 === Summary ===');
        $this->table(
            ['Type', 'Penalties Added', 'Total Amount'],
            [
                ['Student Penalties', $studentPenalties['count'], '₱' . number_format($studentPenalties['amount'], 2)],
                ['Faculty Penalties', $facultyPenalties['count'], '₱' . number_format($facultyPenalties['amount'], 2)],
                ['Total', $studentPenalties['count'] + $facultyPenalties['count'], '₱' . number_format($studentPenalties['amount'] + $facultyPenalties['amount'], 2)],
            ]
        );

        if ($dryRun) {
            $this->warn('⚠️  These penalties were NOT created (dry run mode)');
        } else {
            $this->info('✅ Daily penalty processing completed successfully!');
        }

        return Command::SUCCESS;
    }

    /**
     * Process student overdue penalties
     */
    private function processStudentPenalties($dailyPenalty, $today, $dryRun)
    {
        // Get all currently overdue borrows (not returned)
        $overdueborrows = StudentBorrow::whereDate('due_date', '<', $today)
            ->whereNull('date_returned')
            ->get();

        $count = 0;
        $totalAmount = 0;

        foreach ($overdueborrows as $borrow) {
            // Check if penalty already added TODAY
            $penaltyToday = StudentFine::where('student_id', $borrow->student_id)
                ->where('copy_id', $borrow->copy_id)
                ->whereDate('created_at', $today)
                ->where('reason', 'LIKE', 'Daily Overdue Penalty%')
                ->exists();

            if (!$penaltyToday) {
                $daysOverdue = Carbon::parse($borrow->due_date)->diffInDays(Carbon::today());

                if (!$dryRun) {
                    StudentFine::create([
                        'student_id' => $borrow->student_id,
                        'copy_id' => $borrow->copy_id,
                        'amount' => (string) $dailyPenalty,
                        'reason' => "Daily Overdue Penalty (Day {$daysOverdue})",
                        'status' => 0,
                        'date_paid' => null,
                    ]);
                }

                $count++;
                $totalAmount += $dailyPenalty;

                $student = $borrow->student;
                $book = $borrow->copy->book;
                $this->line("  📚 {$student->first_name} {$student->last_name} - {$book->title} (Day {$daysOverdue}): ₱{$dailyPenalty}");
            }
        }

        return ['count' => $count, 'amount' => $totalAmount];
    }

    /**
     * Process faculty overdue penalties
     */
    private function processFacultyPenalties($dailyPenalty, $today, $dryRun)
    {
        // Get all currently overdue borrows (not returned)
        $overdueborrows = FacultyBorrow::whereDate('due_date', '<', $today)
            ->whereNull('date_returned')
            ->get();

        $count = 0;
        $totalAmount = 0;

        foreach ($overdueborrows as $borrow) {
            // Check if penalty already added TODAY
            $penaltyToday = FacultyFine::where('faculty_id', $borrow->faculty_id)
                ->where('copy_id', $borrow->copy_id)
                ->whereDate('created_at', $today)
                ->where('reason', 'LIKE', 'Daily Overdue Penalty%')
                ->exists();

            if (!$penaltyToday) {
                $daysOverdue = Carbon::parse($borrow->due_date)->diffInDays(Carbon::today());

                if (!$dryRun) {
                    FacultyFine::create([
                        'faculty_id' => $borrow->faculty_id,
                        'copy_id' => $borrow->copy_id,
                        'amount' => (string) $dailyPenalty,
                        'reason' => "Daily Overdue Penalty (Day {$daysOverdue})",
                        'status' => 0,
                        'date_paid' => null,
                    ]);
                }

                $count++;
                $totalAmount += $dailyPenalty;

                $faculty = $borrow->faculty;
                $book = $borrow->copy->book;
                $this->line("  📚 {$faculty->first_name} {$faculty->last_name} - {$book->title} (Day {$daysOverdue}): ₱{$dailyPenalty}");
            }
        }

        return ['count' => $count, 'amount' => $totalAmount];
    }
}
