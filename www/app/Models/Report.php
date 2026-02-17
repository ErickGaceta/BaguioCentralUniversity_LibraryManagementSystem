<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Report extends Model
{
    protected $fillable = [
        'title',
        'report_type',
        'period_preset',
        'date_from',
        'date_to',
        'report_data',
        'total_records',
    ];

    protected $casts = [
        'report_data' => 'array',
        'date_from'   => 'date',
        'date_to'     => 'date',
    ];

    // ── Constants ────────────────────────────────────────────────────────────

    public const TYPES = [
        'issuance'       => 'Issuance Transactions',
        'book_added'     => 'Books Added to Library',
        'book_archived'  => 'Books Archived',
        'fines_student'  => 'Student Fines',
        'fines_faculty'  => 'Faculty Fines',
        'fines_both'     => 'Student & Faculty Fines',
    ];

    public const PRESETS = [
        'annual'      => 'Annual',
        'semi_annual' => 'Semi-Annual',
        'quarterly'   => 'Quarterly',
        'monthly'     => 'Monthly',
        'custom'      => 'Custom Range',
    ];

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Return the date range implied by the given preset, anchored to today.
     *
     * @param  string  $preset
     * @param  int     $year        Used for annual / semi-annual / quarterly.
     * @param  int|null $quarter    1-4, used when preset = quarterly.
     * @param  int|null $month      1-12, used when preset = monthly.
     * @return array{date_from: string, date_to: string}
     */
    public static function datesForPreset(
        string $preset,
        int $year,
        ?int $quarter = null,
        ?int $month = null
    ): array {
        switch ($preset) {
            case 'annual':
                return [
                    'date_from' => Carbon::create($year, 1, 1)->toDateString(),
                    'date_to'   => Carbon::create($year, 12, 31)->toDateString(),
                ];

            case 'semi_annual':
                // First half = Jan–Jun, second half = Jul–Dec
                $half = $quarter ?? 1; // re-use $quarter slot: 1 = first, 2 = second
                if ($half === 1) {
                    return [
                        'date_from' => Carbon::create($year, 1, 1)->toDateString(),
                        'date_to'   => Carbon::create($year, 6, 30)->toDateString(),
                    ];
                }
                return [
                    'date_from' => Carbon::create($year, 7, 1)->toDateString(),
                    'date_to'   => Carbon::create($year, 12, 31)->toDateString(),
                ];

            case 'quarterly':
                $q = $quarter ?? 1;
                $startMonth = (($q - 1) * 3) + 1;
                $start = Carbon::create($year, $startMonth, 1);
                return [
                    'date_from' => $start->toDateString(),
                    'date_to'   => $start->copy()->endOfQuarter()->toDateString(),
                ];

            case 'monthly':
                $m = $month ?? now()->month;
                $start = Carbon::create($year, $m, 1);
                return [
                    'date_from' => $start->toDateString(),
                    'date_to'   => $start->copy()->endOfMonth()->toDateString(),
                ];

            default:
                return [
                    'date_from' => now()->startOfYear()->toDateString(),
                    'date_to'   => now()->toDateString(),
                ];
        }
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getTypeLabel(): string
    {
        return self::TYPES[$this->report_type] ?? $this->report_type;
    }

    public function getPresetLabel(): string
    {
        return self::PRESETS[$this->period_preset] ?? $this->period_preset;
    }
}
