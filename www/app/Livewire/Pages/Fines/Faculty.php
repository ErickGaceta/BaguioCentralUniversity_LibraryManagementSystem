<?php

namespace App\Livewire\Pages\Fines;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\FacultyFine;
use App\Models\FacultyFineArchive;
use App\Services\ArchiveTransactionService;
use App\Services\AutomaticPenaltyService;

use Livewire\Attributes\Lazy;

#[Lazy] class Faculty extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';

    public $penaltiesProcessed = false;
    public $penaltyCount = 0;
    public $fineToPayId = null;
    public $paymentAmount = null;
    public ?int $archivingFineId = null;

    public function mount()
    {
        $result = AutomaticPenaltyService::checkAndProcess();

        if ($result['processed']) {
            $this->penaltiesProcessed = true;
            $this->penaltyCount = $result['total'];

            if ($result['total'] > 0) {
                $daysProcessed = $result['days_processed'];
                $dateRange = $result['date_range'];

                if ($daysProcessed > 1) {
                    session()->flash('success', "Automatic penalty processing: {$result['total']} penalties added for {$daysProcessed} days ({$dateRange}).");
                } else {
                    session()->flash('success', "Automatic penalty processing: {$result['total']} penalty/penalties added.");
                }
            }
        }
    }

    public function archiveConfirmed(): void
    {
        if (!$this->archivingFineId) return;
        $this->archiveFine($this->archivingFineId);
        $this->archivingFineId = null;
    }

    public function placeholder()
    {
        return <<<'HTML'
            <div class="w-full h-full flex justify-center items-center align-center">
                <span class="loader"></span>
            </div>
        HTML;
    }

    public function reprocessPenalties()
    {
        \Illuminate\Support\Facades\Cache::forget('penalties_last_processed');

        $result = AutomaticPenaltyService::checkAndProcess();

        if ($result['processed']) {
            if ($result['total'] > 0) {
                $daysProcessed = $result['days_processed'];
                $dateRange = $result['date_range'];

                if ($daysProcessed > 1) {
                    session()->flash('success', "Manual reprocess: {$result['total']} penalties added for {$daysProcessed} days ({$dateRange}).");
                } else {
                    session()->flash('success', "Manual reprocess: {$result['total']} penalty/penalties added.");
                }
            } else {
                session()->flash('success', "Manual reprocess: No new penalties needed.");
            }
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function openPaymentModal($fineId)
    {
        $fine = FacultyFine::find($fineId);

        if ($fine) {
            $this->fineToPayId = $fineId;
            $this->paymentAmount = $fine->amount;
        }
    }

    public function closePaymentModal()
    {
        $this->fineToPayId = null;
        $this->paymentAmount = null;
        $this->dispatch('close-payment-modal');
    }

    public function markAsPaid()
    {
        $fine = FacultyFine::find($this->fineToPayId);
        if ($fine) {
            $fine->markAsPaid();
            $this->closePaymentModal();
            session()->flash('success', 'Fine marked as paid successfully.');
        }
    }

    public function archiveFine($fineId)
    {
        $fine = FacultyFine::with(['faculty', 'copy.book'])->find($fineId);

        if (!$fine) {
            session()->flash('success', 'Fine not found.');
            return;
        }

        // Copy into archive table
        FacultyFineArchive::create([
            'fine_id'     => $fine->id,
            'faculty_id'  => $fine->faculty_id,
            'copy_id'     => $fine->copy_id,
            'amount'      => $fine->amount,
            'reason'      => $fine->reason,
            'status'      => $fine->status,
            'date_paid'   => $fine->date_paid,
            'archived_at' => now(),
        ]);

        // Log to library transactions
        $facultyName = $fine->faculty->first_name . ' ' . $fine->faculty->last_name;
        $bookTitle   = $fine->copy->book->title ?? 'Unknown Book';
        $amount      = number_format($fine->amount, 2);
        $status      = $fine->isPaid() ? 'Paid' : 'Unpaid';

        ArchiveTransactionService::record(
            'faculty_fine',
            "{$facultyName} ({$fine->faculty_id}) - {$bookTitle} - ₱{$amount} [{$status}]"
        );

        $fine->delete();

        session()->flash('success', 'Fine has been archived.');
    }

    public function render()
    {
        $fines = FacultyFine::with(['faculty', 'copy.book'])
            ->when($this->search, function ($query) {
                $query->whereHas('faculty', function ($q) {
                    $searchTerm = "%{$this->search}%";
                    $q->where('faculty_id', 'LIKE', $searchTerm)
                        ->orWhere('first_name', 'LIKE', $searchTerm)
                        ->orWhere('middle_name', 'LIKE', $searchTerm)
                        ->orWhere('last_name', 'LIKE', $searchTerm);
                });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $status = $this->statusFilter === 'paid' ? 1 : 0;
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $allFines    = FacultyFine::all();
        $totalUnpaid = $allFines->where('status', 0)->sum('amount');
        $totalPaid   = $allFines->where('status', 1)->sum('amount');
        $countUnpaid = $allFines->where('status', 0)->count();

        return view('livewire.pages.fines.faculty', [
            'fines'       => $fines,
            'totalUnpaid' => $totalUnpaid,
            'totalPaid'   => $totalPaid,
            'countUnpaid' => $countUnpaid,
        ]);
    }
}
