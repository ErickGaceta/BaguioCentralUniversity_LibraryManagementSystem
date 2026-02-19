<?php

namespace App\Livewire\Pages\Books;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\CopyAccession;

class BookCatalog extends Component
{
    public bool   $show      = false;
    public int    $copies    = 0;
    public string $bookTitle = '';

    /**
     * Fired by BookCreate after its form passes validation.
     * Opens this modal and sets how many copy rows to render.
     */
    #[On('open-book-catalog')]
    public function open(int $copies, string $title): void
    {
        $this->copies    = $copies;
        $this->bookTitle = $title;
        $this->show      = true;
    }

    public function close(): void
    {
        $this->show = false;
    }

    /**
     * Called by Alpine, receives the array of accession/call number pairs.
     * Validates them, then fires catalog-ready so BookCreate can persist everything.
     */
    public function save(array $copyData): void
    {
        // All fields filled
        foreach ($copyData as $i => $row) {
            $num = $i + 1;
            if (empty(trim($row['accession_number'] ?? ''))) {
                $this->addError('catalog', "Copy #{$num}: Accession number is required.");
                return;
            }
            if (empty(trim($row['call_number'] ?? ''))) {
                $this->addError('catalog', "Copy #{$num}: Call number is required.");
                return;
            }
        }

        // No duplicates within batch
        $accessionNumbers = array_column($copyData, 'accession_number');
        if (count($accessionNumbers) !== count(array_unique($accessionNumbers))) {
            $this->addError('catalog', 'Duplicate accession numbers found. Each copy must be unique.');
            return;
        }

        $existing = CopyAccession::whereIn('accession_number', $accessionNumbers)
            ->pluck('accession_number')
            ->toArray();

        if (!empty($existing)) {
            $this->addError('catalog', 'These accession numbers already exist: ' . implode(', ', $existing));
            return;
        }

        $this->dispatch('catalog-ready', copyData: $copyData);

        $this->show = false;
    }

    public function render()
    {
        return view('livewire.pages.books.book-catalog');
    }
}
