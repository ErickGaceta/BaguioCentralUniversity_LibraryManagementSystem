<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Daily Overdue Penalty Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the daily penalty for overdue books
    | This penalty is automatically added every day at 10:00 AM
    |
    */

    'daily_overdue_penalty' => env('LIBRARY_DAILY_OVERDUE_PENALTY', 20.00),

    /*
    |--------------------------------------------------------------------------
    | Damage Fine Amounts
    |--------------------------------------------------------------------------
    |
    | Automatically applied when books are returned with damage
    |
    */

    'damage_fines' => [
        'minor' => env('LIBRARY_MINOR_DAMAGE', 50.00),
        'major' => env('LIBRARY_MAJOR_DAMAGE', 150.00),
        'total' => env('LIBRARY_TOTAL_DAMAGE', 500.00),
    ],

    /*
    |--------------------------------------------------------------------------
    | Penalty Processing Schedule
    |--------------------------------------------------------------------------
    */

    'penalty_processing_time' => env('LIBRARY_PENALTY_TIME', '10:00'),
];
