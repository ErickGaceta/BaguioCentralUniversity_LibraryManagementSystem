<?php

namespace App\Livewire\Pages\Fines;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\FacultyFine;
use App\Models\Faculty as FacultyModel;
use App\Services\AutomaticPenaltyService;

class Faculty extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all'; // all, paid, unpaid
    public $showPaymentModal = false;

    // Payment
    public $fineToPayId;
    public $paymentAmount;

    // Penalty processing status
    public $penaltiesProcessed = false;
    public $penaltyCount = 0;

    public function mount()
    {
        // Automatically check and process penalties when page loads
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

    public function reprocessPenalties()
    {
        // Clear cache and force reprocessing
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
            $this->showPaymentModal = true;
        }
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->fineToPayId = null;
        $this->paymentAmount = null;
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

    public function deleteFine($fineId)
    {
        FacultyFine::find($fineId)->delete();
        session()->flash('success', 'Fine deleted successfully.');
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

        // Calculate statistics
        $allFines = FacultyFine::all();
        $totalUnpaid = $allFines->where('status', 0)->sum('amount');
        $totalPaid = $allFines->where('status', 1)->sum('amount');
        $countUnpaid = $allFines->where('status', 0)->count();

        return view('livewire.pages.fines.faculty', [
            'fines' => $fines,
            'totalUnpaid' => $totalUnpaid,
            'totalPaid' => $totalPaid,
            'countUnpaid' => $countUnpaid,
        ]);
    }
}
