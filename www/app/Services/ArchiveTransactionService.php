<?php

namespace App\Services;

use App\Models\LibraryTransaction;
use Illuminate\Support\Str;

class ArchiveTransactionService
{
    /**
     * Prefixes per archive type.
     */
    const PREFIXES = [
        'book'        => 'BOOK-ARCH',
        'student'     => 'STDNT-ARCH',
        'faculty'     => 'FCLTY-ARCH',
        'issuance'    => 'ISS-ARCH',
        'library'     => 'LIB-ARCH',
    ];

    /**
     * Generate a unique reference number.
     * Format: PREFIX-<15 uppercase alphanumeric chars>
     * e.g. STDNT-ARCH-A3F9K2B7QX1M8WP
     */
    public static function generateRefNumber(string $type): string
    {
        $prefix = self::PREFIXES[$type] ?? 'ARCH';
        $suffix = strtoupper(Str::random(15));

        return "{$prefix}-{$suffix}";
    }

    /**
     * Record an archive action in library_transactions.
     *
     * @param  string  $type        One of: book, student, faculty, issuance, library
     * @param  string  $description Human-readable detail, e.g. the name / ID being archived
     * @return LibraryTransaction
     */
    public static function record(string $type, string $description): LibraryTransaction
    {
        $labels = [
            'book'     => 'Book Archived',
            'student'  => 'Student Archived',
            'faculty'  => 'Faculty Archived',
            'issuance' => 'Issuance Transaction Archived',
            'library'  => 'Library Transaction Archived',
        ];

        $transactionName = ($labels[$type] ?? 'Archived') . ' - ' . $description . ' - By Admin';

        $refNumber = self::generateRefNumber($type);

        return LibraryTransaction::create([
            'transaction_name' => $transactionName,
            'ref_number'       => $refNumber,
        ]);
    }
}
