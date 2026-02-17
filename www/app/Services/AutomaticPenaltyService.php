<?php

namespace App\Services;

use App\Models\StudentBorrow;
use App\Models\FacultyBorrow;
use App\Models\StudentFine;
use App\Models\FacultyFine;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AutomaticPenaltyService
{
    /**
     * Check and process daily penalties if needed
     * Adds penalties for ALL missed days since last processing
     */
    public static function checkAndProcess(): array
    {
        $today = Carbon::today();
        $currentTime = Carbon::now();
        $processingTime = Carbon::today()->setTime(10, 0); // 10:00 AM

        // Only process if current time is past 10:00 AM
        if ($currentTime->lt($processingTime)) {
            return [
                'processed' => false,
                'reason' => 'Before 10:00 AM',
                'next_check' => $processingTime->format('h:i A'),
            ];
        }

        // Get last processed date
        $lastProcessed = Cache::get('penalties_last_processed');
        $lastProcessedDate = $lastProcessed ? Carbon::parse($lastProcessed) : null;

        // Check if already processed today
        if ($lastProcessedDate && $lastProcessedDate->isSameDay($today)) {
            return [
                'processed' => false,
                'reason' => 'Already processed today',
                'last_processed' => $lastProcessed,
            ];
        }

        // Calculate days to process
        $daysToProcess = self::calculateDaysToProcess($lastProcessedDate, $today);

        // Process penalties for each missed day
        $totalStudentPenalties = 0;
        $totalFacultyPenalties = 0;

        foreach ($daysToProcess as $dateToProcess) {
            $result = self::processPenaltiesForDate($dateToProcess);
            $totalStudentPenalties += $result['student_count'];
            $totalFacultyPenalties += $result['faculty_count'];
        }

        // Mark today as processed
        Cache::put('penalties_last_processed', $today->format('Y-m-d'), now()->addDays(7));

        Log::info('Automatic daily penalties processed', [
            'days_processed' => count($daysToProcess),
            'date_range' => count($daysToProcess) > 1
                ? $daysToProcess[0]->format('Y-m-d') . ' to ' . $today->format('Y-m-d')
                : $today->format('Y-m-d'),
            'student_penalties' => $totalStudentPenalties,
            'faculty_penalties' => $totalFacultyPenalties,
        ]);

        return [
            'processed' => true,
            'days_processed' => count($daysToProcess),
            'date_range' => count($daysToProcess) > 1
                ? $daysToProcess[0]->format('M d') . ' to ' . $today->format('M d')
                : $today->format('M d'),
            'student_penalties' => $totalStudentPenalties,
            'faculty_penalties' => $totalFacultyPenalties,
            'total' => $totalStudentPenalties + $totalFacultyPenalties,
        ];
    }

    /**
     * Calculate which days need to be processed
     */
    private static function calculateDaysToProcess($lastProcessedDate, Carbon $today): array
    {
        $daysToProcess = [];

        if (!$lastProcessedDate) {
            // No last processed date - need to check ALL overdue books
            // Find the earliest overdue book and process from there

            $earliestStudentDue = StudentBorrow::whereNull('date_returned')
                ->whereDate('due_date', '<', $today->format('Y-m-d'))
                ->orderBy('due_date', 'asc')
                ->value('due_date');

            $earliestFacultyDue = FacultyBorrow::whereNull('date_returned')
                ->whereDate('due_date', '<', $today->format('Y-m-d'))
                ->orderBy('due_date', 'asc')
                ->value('due_date');

            // Find the earliest due date
            $earliestDueDate = null;
            if ($earliestStudentDue && $earliestFacultyDue) {
                $earliestDueDate = min($earliestStudentDue, $earliestFacultyDue);
            } elseif ($earliestStudentDue) {
                $earliestDueDate = $earliestStudentDue;
            } elseif ($earliestFacultyDue) {
                $earliestDueDate = $earliestFacultyDue;
            }

            if ($earliestDueDate) {
                // Start processing from the day after the earliest due date
                $startDate = Carbon::parse($earliestDueDate)->addDay();
                $currentDate = $startDate->copy();

                while ($currentDate->lte($today)) {
                    $daysToProcess[] = $currentDate->copy();
                    $currentDate->addDay();
                }
            } else {
                // No overdue books, just process today if needed
                $daysToProcess[] = $today->copy();
            }
        } else {
            // Process all days from (last processed + 1 day) to today
            $startDate = $lastProcessedDate->copy()->addDay();
            $currentDate = $startDate->copy();

            while ($currentDate->lte($today)) {
                $daysToProcess[] = $currentDate->copy();
                $currentDate->addDay();
            }
        }

        return $daysToProcess;
    }

    /**
     * Process penalties for a specific date
     */
    private static function processPenaltiesForDate(Carbon $dateToProcess): array
    {
        $dateString = $dateToProcess->format('Y-m-d');
        $dailyPenalty = config('library.daily_overdue_penalty', 20.00);

        $studentCount = 0;
        $facultyCount = 0;

        // Process student penalties
        $studentCount = self::processStudentPenaltiesForDate($dateToProcess, $dailyPenalty);

        // Process faculty penalties
        $facultyCount = self::processFacultyPenaltiesForDate($dateToProcess, $dailyPenalty);

        return [
            'date' => $dateString,
            'student_count' => $studentCount,
            'faculty_count' => $facultyCount,
        ];
    }

    /**
     * Process student overdue penalties for a specific date
     */
    private static function processStudentPenaltiesForDate(Carbon $dateToProcess, $dailyPenalty): int
    {
        $dateString = $dateToProcess->format('Y-m-d');
        $count = 0;

        // Get all borrows that were overdue on this specific date
        $overdueborrows = StudentBorrow::whereDate('due_date', '<', $dateString)
            ->where(function ($query) use ($dateString) {
                $query->whereNull('date_returned')
                    ->orWhereDate('date_returned', '>=', $dateString);
            })
            ->get();

        foreach ($overdueborrows as $borrow) {
            // Check if penalty already added for THIS SPECIFIC DATE
            $penaltyExists = StudentFine::where('student_id', $borrow->student_id)
                ->where('copy_id', $borrow->copy_id)
                ->whereDate('created_at', $dateString)
                ->where('reason', 'LIKE', 'Daily Overdue Penalty%')
                ->exists();

            if (!$penaltyExists) {
                $daysOverdue = Carbon::parse($borrow->due_date)->diffInDays($dateToProcess);

                // Use DB::table to insert with specific created_at date
                // This bypasses Eloquent's automatic timestamp handling
                DB::table('student_fines')->insert([
                    'student_id' => $borrow->student_id,
                    'copy_id' => $borrow->copy_id,
                    'amount' => (string) $dailyPenalty,
                    'reason' => "Daily Overdue Penalty (Day {$daysOverdue})",
                    'status' => 0,
                    'date_paid' => null,
                    'created_at' => $dateToProcess,
                    'updated_at' => $dateToProcess,
                ]);

                $count++;
            }
        }

        return $count;
    }

    /**
     * Process faculty overdue penalties for a specific date
     */
    private static function processFacultyPenaltiesForDate(Carbon $dateToProcess, $dailyPenalty): int
    {
        $dateString = $dateToProcess->format('Y-m-d');
        $count = 0;

        // Get all borrows that were overdue on this specific date
        $overdueborrows = FacultyBorrow::whereDate('due_date', '<', $dateString)
            ->where(function ($query) use ($dateString) {
                $query->whereNull('date_returned')
                    ->orWhereDate('date_returned', '>=', $dateString);
            })
            ->get();

        foreach ($overdueborrows as $borrow) {
            // Check if penalty already added for THIS SPECIFIC DATE
            $penaltyExists = FacultyFine::where('faculty_id', $borrow->faculty_id)
                ->where('copy_id', $borrow->copy_id)
                ->whereDate('created_at', $dateString)
                ->where('reason', 'LIKE', 'Daily Overdue Penalty%')
                ->exists();

            if (!$penaltyExists) {
                $daysOverdue = Carbon::parse($borrow->due_date)->diffInDays($dateToProcess);

                // Use DB::table to insert with specific created_at date
                // This bypasses Eloquent's automatic timestamp handling
                DB::table('faculty_fines')->insert([
                    'faculty_id' => $borrow->faculty_id,
                    'copy_id' => $borrow->copy_id,
                    'amount' => (string) $dailyPenalty,
                    'reason' => "Daily Overdue Penalty (Day {$daysOverdue})",
                    'status' => 0,
                    'date_paid' => null,
                    'created_at' => $dateToProcess,
                    'updated_at' => $dateToProcess,
                ]);

                $count++;
            }
        }

        return $count;
    }

    /**
     * Get the last processed date
     */
    public static function getLastProcessedDate(): ?string
    {
        return Cache::get('penalties_last_processed');
    }

    /**
     * Check if penalties have been processed today
     */
    public static function processedToday(): bool
    {
        $today = Carbon::today()->format('Y-m-d');
        return Cache::get('penalties_last_processed') === $today;
    }
}
