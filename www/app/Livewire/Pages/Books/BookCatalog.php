<?php

namespace App\Livewire\Pages\Books;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\CopyAccession;

class BookCatalog extends Component
{
    public bool   $show           = false;
    public string $bookTitle      = '';
    public array  $existingCopies = []; // [{copy_id, label}] — existing copies missing accession records
    public int    $newCopiesCount = 0;  // brand-new copies being added

    /**
     * Fired by BookCreate or BookEdit after their forms pass validation.
     *
     * $existingCopies : existing Copy rows that have no CopyAccession yet
     * $newCopiesCount : how many new copies are being added this save
     */
    #[On('open-book-catalog')]
    public function open(string $title, array $existingCopies = [], int $newCopiesCount = 0): void
    {
        $this->bookTitle      = $title;
        $this->existingCopies = $existingCopies;
        $this->newCopiesCount = $newCopiesCount;
        $this->show           = true;
        $this->resetErrorBag();
    }

    public function close(): void
    {
        $this->show = false;
    }

    /**
     * Called by Alpine with two separate arrays:
     *
     * $existingCatalogData : [{copy_id, label, accession_number, call_number}]
     * $newCopyData         : [{accession_number, call_number}]
     */
    public function save(array $existingCatalogData = [], array $newCopyData = []): void
    {
        $allAccessions = array_merge(
            array_column($existingCatalogData, 'accession_number'),
            array_column($newCopyData, 'accession_number'),
        );

        // All fields filled — existing uncataloged rows
        foreach ($existingCatalogData as $row) {
            $label = $row['label'] ?? $row['copy_id'];
            if (empty(trim($row['accession_number'] ?? ''))) {
                $this->addError('catalog', "{$label}: Accession number is required.");
                return;
            }
            if (empty(trim($row['call_number'] ?? ''))) {
                $this->addError('catalog', "{$label}: Call number is required.");
                return;
            }
        }

        // All fields filled — new copy rows
        $offset = count($existingCatalogData);
        foreach ($newCopyData as $i => $row) {
            $num = $offset + $i + 1;
            if (empty(trim($row['accession_number'] ?? ''))) {
                $this->addError('catalog', "New Copy #{$num}: Accession number is required.");
                return;
            }
            if (empty(trim($row['call_number'] ?? ''))) {
                $this->addError('catalog', "New Copy #{$num}: Call number is required.");
                return;
            }
        }

        // No duplicates within batch
        if (count($allAccessions) !== count(array_unique($allAccessions))) {
            $this->addError('catalog', 'Duplicate accession numbers found. Each copy must be unique.');
            return;
        }

        // No conflicts with DB
        $conflict = CopyAccession::whereIn('accession_number', $allAccessions)
            ->pluck('accession_number')
            ->toArray();

        if (!empty($conflict)) {
            $this->addError('catalog', 'These accession numbers already exist: ' . implode(', ', $conflict));
            return;
        }

        $this->dispatch(
            'catalog-ready',
            existingCatalogData: $existingCatalogData,
            newCopyData: $newCopyData,
        );

        $this->show = false;
    }

    public function render()
    {
        return view('livewire.pages.books.book-catalog');
    }
}
