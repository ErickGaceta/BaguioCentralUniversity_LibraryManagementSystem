<?php

namespace App\Livewire\Pages\Transactions;

use App\Models\LibraryTransaction;
use App\Models\TransactionArchive;
use App\Services\ArchiveTransactionService;
use Livewire\Component;
use Livewire\WithPagination;

use Livewire\Attributes\Lazy;

#[Lazy] class Library extends Component
{
    use WithPagination;

    public $perPage = 15;

    public function archiveLibraryTransaction($id)
    {
        $transaction = LibraryTransaction::find($id);

        if ($transaction) {
            TransactionArchive::create([
                'library_transaction_id' => (string) $transaction->id,
                'name' => $transaction->transaction_name . ' - Ref: ' . $transaction->ref_number,
            ]);

            ArchiveTransactionService::record('library', "{$transaction->transaction_name} Ref: {$transaction->ref_number}");

            $transaction->delete();

            session()->flash('message', 'Library transaction has been archived.');
        }
    }

    public function placeholder()
    {
        return <<<'HTML'
            <div class="w-full h-full flex justify-center items-center align-center">
                <span class="loader"></span>
            </div>
        HTML;
    }

    public ?int $archivingLibraryTransactionId = null;

    public function prepareArchiveLibraryTransaction(int $id): void
    {
        $this->archivingLibraryTransactionId = $id;
        $this->dispatch('open-archive-library-transaction-modal');
    }

    public function archiveLibraryTransactionConfirmed(): void
    {
        if (!$this->archivingLibraryTransactionId) return;

        $this->archiveLibraryTransaction($this->archivingLibraryTransactionId);
        $this->archivingLibraryTransactionId = null;
    }

    public function render()
    {
        $libTransactions = LibraryTransaction::orderBy('created_at', 'desc')->paginate($this->perPage);

        return view('livewire.pages.transactions.library', [
            'libTransactions' => $libTransactions,
        ]);
    }
}
