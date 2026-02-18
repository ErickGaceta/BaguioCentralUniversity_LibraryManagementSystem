<?php

namespace App\Services;

use App\Models\Report;
use TCPDF;

class ReportPdf extends TCPDF
{
    protected Report $report;

    // ── TCPDF Header & Footer stubs ───────────────────────────────────────────
    // Fill these in later with your institution branding.

    public function Header(): void
    {
        // Example:
        // $this->Image(public_path('favicon.ico'), 10, 6, 20);
        // $this->SetFont('dejavusans', 'B', 14);
        // $this->Cell(0, 10, 'Baguio Central University Library', 0, 1, 'C');
        // $this->Ln(2);
    }

    public function Footer(): void
    {
        // Example:
        // $this->SetY(-15);
        // $this->SetFont('dejavusans', 'I', 8);
        // $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 0, 'C');
    }

    // ── Entry point ───────────────────────────────────────────────────────────

    public static function generate(Report $report): self
    {
        $pdf = new self('P', 'mm', 'A4', true, 'UTF-8');

        $pdf->report = $report;

        $pdf->SetCreator('BCU Library Management System');
        $pdf->SetAuthor('BCU Library');
        $pdf->SetTitle($report->title);
        $pdf->SetSubject($report->getTypeLabel());

        $pdf->SetMargins(15, 20, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        $pdf->SetAutoPageBreak(true, 20);

        $pdf->AddPage();

        // ── Report title block ────────────────────────────────────────────────

        $pdf->SetFont('dejavusans', 'B', 16);
        $pdf->Cell(0, 10, $report->title, 0, 1, 'C');

        $pdf->SetFont('dejavusans', '', 9);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(
            0,
            6,
            'Period: ' . $report->date_from->format('M d, Y')
                . ' – ' . $report->date_to->format('M d, Y')
                . '   |   Type: ' . $report->getTypeLabel()
                . '   |   Records: ' . number_format($report->total_records),
            0,
            1,
            'C'
        );
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(4);

        // ── Route to the correct table renderer ───────────────────────────────

        $data = $report->report_data ?? [];

        match ($report->report_type) {
            'issuance'      => $pdf->renderIssuanceTable($data),
            'book_added'    => $pdf->renderBooksTable($data, 'Books Added to Library'),
            'book_archived' => $pdf->renderBooksTable($data, 'Books Archived'),
            'fines_student' => $pdf->renderFinesTable($data, false),
            'fines_faculty' => $pdf->renderFinesTable($data, false),
            'fines_both'    => $pdf->renderFinesTable($data, true),
            default         => null,
        };

        return $pdf;
    }

    // ── Card renderers ────────────────────────────────────────────────────────

    private function renderIssuanceTable(array $rows): void
    {
        if (empty($rows)) {
            $this->renderEmptyState('No issuance records found for this period.');
            return;
        }

        foreach ($rows as $index => $row) {
            $row = (array) $row;

            // Check if we need a new page
            if ($this->GetY() > 240) {
                $this->AddPage();
            }

            $this->renderCard(
                header: $row['ref_number'] ?? 'N/A',
                fields: [
                    ['Type', $row['borrower_type'] ?? '—'],
                    ['Borrower', $row['borrower_name'] ?? '—'],
                    ['Book Title', $row['book_title'] ?? '—'],
                    ['Date Borrowed', $row['date_borrowed'] ? date('M d, Y', strtotime($row['date_borrowed'])) : '—'],
                    ['Due Date', $row['due_date'] ? date('M d, Y', strtotime($row['due_date'])) : '—'],
                    ['Date Returned', $row['date_returned'] ? date('M d, Y', strtotime($row['date_returned'])) : 'Not yet returned'],
                ]
            );

            $this->Ln(3);
        }
    }

    private function renderBooksTable(array $rows, string $heading): void
    {
        if (empty($rows)) {
            $this->renderEmptyState('No books found for this period.');
            return;
        }

        foreach ($rows as $index => $row) {
            $row = (array) $row;

            if ($this->GetY() > 240) {
                $this->AddPage();
            }

            $this->renderCard(
                header: $row['title'] ?? 'Untitled',
                fields: [
                    ['Author', $row['author'] ?? '—'],
                    ['ISBN', $row['isbn'] ?? '—'],
                    ['Publisher', $row['publisher'] ?? '—'],
                    ['Copies', $row['copies'] ?? '—'],
                    ['Date Added', $row['created_at'] ? date('M d, Y', strtotime($row['created_at'])) : '—'],
                ]
            );

            $this->Ln(3);
        }
    }

    private function renderFinesTable(array $rows, bool $showType): void
    {
        if (empty($rows)) {
            $this->renderEmptyState('No fines found for this period.');
            return;
        }

        foreach ($rows as $index => $row) {
            $row = (array) $row;

            if ($this->GetY() > 240) {
                $this->AddPage();
            }

            $status = ($row['status'] ?? 0) ? 'Paid' : 'Unpaid';
            $statusColor = ($row['status'] ?? 0) ? [34, 197, 94] : [239, 68, 68]; // green or red

            $fields = [
                ['Name', $row['name'] ?? '—'],
                ['Book Title', $row['book_title'] ?? '—'],
                ['Amount', '₱ ' . number_format((float)($row['amount'] ?? 0), 2)],
                ['Reason', $row['reason'] ?? '—'],
                ['Status', $status, $statusColor],
                ['Date Paid', !empty($row['date_paid']) ? date('M d, Y', strtotime($row['date_paid'])) : '—'],
            ];

            if ($showType) {
                array_unshift($fields, ['Type', $row['borrower_type'] ?? '—']);
            }

            $this->renderCard(
                header: 'Fine #' . ($row['id'] ?? 'N/A'),
                fields: $fields
            );

            $this->Ln(3);
        }
    }

    // ── Card rendering helpers ────────────────────────────────────────────────

    private function renderCard(string $header, array $fields): void
    {
        $x = $this->GetX();
        $y = $this->GetY();
        $width = 180; // Full page width minus margins
        $headerHeight = 8;

        // Calculate card body height based on number of fields
        $rowHeight = 6;
        $bodyHeight = count($fields) * $rowHeight + 4;
        $totalHeight = $headerHeight + $bodyHeight;

        // Draw card border
        $this->SetDrawColor(200, 200, 200);
        $this->SetLineWidth(0.2);
        $this->Rect($x, $y, $width, $totalHeight, 'D');

        // Draw header background
        $this->SetFillColor(134, 8, 5); // BCU maroon
        $this->Rect($x, $y, $width, $headerHeight, 'F');

        // Header text
        $this->SetTextColor(234, 224, 210); // BCU cream
        $this->SetFont('dejavusans', 'B', 10);
        $this->SetXY($x + 3, $y + 2);
        $this->Cell($width - 6, $headerHeight - 4, $header, 0, 0, 'L');

        // Reset text color for body
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('dejavusans', '', 9);

        // Render fields
        $currentY = $y + $headerHeight + 2;

        foreach ($fields as $field) {
            $label = $field[0];
            $value = $field[1];
            $customColor = $field[2] ?? null;

            $this->SetXY($x + 3, $currentY);

            // Label (bold)
            $this->SetFont('dejavusans', 'B', 9);
            $this->SetTextColor(100, 100, 100);
            $this->Cell(40, $rowHeight, $label . ':', 0, 0, 'L');

            // Value
            $this->SetFont('dejavusans', '', 9);

            if ($customColor) {
                $this->SetTextColor($customColor[0], $customColor[1], $customColor[2]);
            } else {
                $this->SetTextColor(0, 0, 0);
            }

            $this->SetX($x + 45);
            $this->MultiCell($width - 50, $rowHeight, $value, 0, 'L', false, 1);

            $currentY += $rowHeight;
        }

        // Move cursor below card
        $this->SetXY($x, $y + $totalHeight);
    }

    private function renderEmptyState(string $message): void
    {
        $this->SetFont('dejavusans', 'I', 10);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 10, $message, 0, 1, 'C');
        $this->SetTextColor(0, 0, 0);
    }
}
