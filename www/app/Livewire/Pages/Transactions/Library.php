<?php

namespace App\Livewire\Pages\Transactions;

use App\Models\LibraryTransaction;
use Livewire\Component;

class Library extends Component
{
    public $perPage = 15;
    public function render()
    {
        $libTransactions = LibraryTransaction::orderBy("created_at","desc")->paginate($this->perPage);
        return view('livewire.pages.transactions.library', [
            'libTransactions'=> $libTransactions
        ]);
    }
}
