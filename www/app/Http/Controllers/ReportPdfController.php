<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\ReportPdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Services\ReportDataService;


class ReportPdfController extends Controller
{
    public function show(Report $report)
    {
        $pdf = ReportPdf::generate($report);

        $filename = $report->title . '.pdf';

        return response(
            $pdf->Output('', 'S'),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]
        );
    }

    public function preview(Request $request, ReportDataService $dataService)
    {
        // Validate the request inputs
        $request->validate([
            'type'   => 'required|string|in:' . implode(',', array_keys(Report::TYPES)),
            'preset' => 'nullable|string',
            'from'   => 'required|date',
            'to'     => 'required|date',
        ]);

        // Prepare the report data
        $report = new Report([
            'title'         => 'Report - ' . (Report::TYPES[$request->type] ?? 'Report'),
            'report_type'   => $request->type,
            'period_preset' => $request->preset,
            'date_from'     => $request->from,
            'date_to'       => $request->to,
        ]);

        $dateFrom = \Carbon\Carbon::parse($request->from)->startOfDay();
        $dateTo   = \Carbon\Carbon::parse($request->to)->endOfDay();

        // Collect data safely
        [$data, $total] = $dataService->collect($report->report_type, $dateFrom, $dateTo);

        $report->report_data   = $data;
        $report->total_records = $total;

        // Return the preview view
        return view('livewire.pages.reports.preview', [
            'report' => $report,
        ]);
    }
}
