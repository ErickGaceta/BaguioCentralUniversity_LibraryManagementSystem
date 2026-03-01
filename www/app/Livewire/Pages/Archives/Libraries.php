<?php

namespace App\Livewire\Pages\Archives;

use App\Models\ArchivesLibrary;
use App\Models\Book;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;

#[Lazy] class Libraries extends Component
{
    use WithPagination;

    public ?int $restoringId = null;

    public function restoreConfirmed(): void
    {
        if (!$this->restoringId) return;
        $this->restoreBook($this->restoringId);
        $this->restoringId = null;
    }

    public function restoreBook(int $bookId): void
    {
        $archivedBook = ArchivesLibrary::findOrFail($bookId);

        Book::create([
            'title'            => $archivedBook->title,
            'author'           => $archivedBook->author,
            'publication_date' => $archivedBook->publication_date,
            'publisher'        => $archivedBook->publisher,
            'isbn'             => $archivedBook->isbn,
            'department_id'    => $archivedBook->department_id,
            'category'         => $archivedBook->category ?? 'General',
            'copies'           => $archivedBook->copies,
        ]);

        $archivedBook->delete();

        session()->flash('message', 'Book restored successfully!');
    }

    public function placeholder()
    {
        return <<<'HTML'
            <div class="w-full h-full flex justify-center items-center align-center">
                <span class="loader"></span>
            </div>
        HTML;
    }

    public function render()
    {
        $archivedBooks = ArchivesLibrary::with('department')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.pages.archives.libraries', [
            'archivedBooks' => $archivedBooks,
        ]);
    }
}
