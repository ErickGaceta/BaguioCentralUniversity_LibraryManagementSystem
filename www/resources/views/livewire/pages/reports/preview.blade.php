<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 11px;
        color: #000;
    }

    .report-title {
        font-size: 14px;
        text-align: center;
        font-weight: bold;
        line-height: 2;
        margin-bottom: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 5px 7px;
        vertical-align: top;
        font-size: 11px;
    }

    th {
        background-color: #f2f2f2;
        font-weight: bold;
        text-align: left;
    }

    .section-header {
        background-color: #d9d9d9;
        font-weight: bold;
        padding: 5px 7px;
        margin-top: 15px;
        font-size: 12px;
    }

    .spacer {
        height: 10px;
    }

    .text {
        line-height: 1.2;
        margin-bottom: 5px;
        display: block;
    }

    .signature-table td {
        border: none;
        padding: 2px 7px;
    }

    .underline {
        border-bottom: 1px solid #000;
        display: inline-block;
        min-width: 200px;
    }
</style>

<div class="report-title">{{ $report->title }}</div>

@if ($report->report_type === 'issuance')
<table>
    <thead>
        <tr>
            <th>Ref. Number</th>
            <th>Borrower Type</th>
            <th>Borrower Name</th>
            <th>Book Title</th>
            <th>Borrowed</th>
            <th>Due</th>
            <th>Returned</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($report->report_data as $row)
        <tr>
            <td>{{ $report['ref_number'] }}</td>
            <td>{{ $row['borrower_type'] }}</td>
            <td>{{ $row['borrower_name'] }}</td>
            <td>{{ $row['book_title'] }}</td>
            <td>{{ \Carbon\Carbon::parse($row['date_borrowed'])->format('M d, Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($row['due_date'])->format('M d, Y') }}</td>
            <td>{{ $row['date_returned'] ? \Carbon\Carbon::parse($row['date_returned'])->format('M d, Y') : '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center;">No records found</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endif

@if (in_array($report->report_type, ['book_added','book_archived']))
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Author</th>
            <th>ISBN</th>
            <th>Publisher</th>
            <th>Copies</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($report->report_data as $row)
        <tr>
            <td>{{ $row->id }}</td>
            <td>{{ $row->title }}</td>
            <td>{{ $row->author }}</td>
            <td>{{ $row->isbn }}</td>
            <td>{{ $row->publisher }}</td>
            <td>{{ $row->copies }}</td>
            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('M d, Y') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center;">No records found</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endif

@if (str_starts_with($report->report_type, 'fines'))
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Borrower Type / Name</th>
            <th>Book Title</th>
            <th>Amount</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Date Paid</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($report->report_data as $row)
        <tr>
            <td>{{ $row->id }}</td>
            <td>{{ $row->borrower_type ?? '' }} {{ $row->name ?? '' }}</td>
            <td>{{ $row->book_title }}</td>
            <td>{{ number_format($row->amount,2) }}</td>
            <td>{{ $row->reason }}</td>
            <td>{{ $row->status }}</td>
            <td>{{ $row->date_paid ? \Carbon\Carbon::parse($row->date_paid)->format('M d, Y') : '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center;">No records found</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endif

