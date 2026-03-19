<?php

namespace App\Livewire\Pages\Archives;

use App\Models\ArchivesCopy;
use App\Models\ArchivesLibrary;
use App\Models\Book;
use App\Models\Copy;
use App\Models\LibraryTransaction;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;

#[Lazy] class Libraries extends Component
{
    use WithPagination;

    public ?int $restoringId = null;
    public ?int $deletingId  = null;
    public array $selectedIds = [];
    public bool $selectAll    = false;

    // ── Restore ──────────────────────────────────────────────────────────────

    public function restoreConfirmed(): void
    {
        if (!$this->restoringId) return;
        $this->restoreBook($this->restoringId);
        $this->restoringId = null;
    }

    public function restoreBook(int $bookId): void
    {
        $archivedBook = ArchivesLibrary::findOrFail($bookId);

        // Restore the book record
        $restoredBook = Book::create([
            'title'            => $archivedBook->title,
            'author'           => $archivedBook->author,
            'publication_date' => $archivedBook->publication_date,
            'publisher'        => $archivedBook->publisher,
            'isbn'             => $archivedBook->isbn,
            'department_id'    => $archivedBook->department_id,
            'category'         => $archivedBook->category ?? 'General',
            'copies'           => $archivedBook->copies,
        ]);

        // Restore all archived copies tied to this archived book
        $archivedCopies = ArchivesCopy::where('archived_book_id', $archivedBook->id)->get();

        foreach ($archivedCopies as $archivedCopy) {
            Copy::create([
                'copy_id'   => $archivedCopy->copy_id,
                'book_id'   => $restoredBook->id,
                'course_id' => $archivedCopy->course_id,
                'status'    => $archivedCopy->status,
                'condition' => $archivedCopy->condition,
            ]);
        }

        LibraryTransaction::create([
            'transaction_name' => "Book Restored - \"{$archivedBook->title}\" by {$archivedBook->author} - By Admin",
            'ref_number'       => $this->generateUniqueRefNumber(),
        ]);

        // Cascade delete removes archives_copies rows too (via FK)
        $archivedBook->delete();

        session()->flash('message', 'Book restored successfully!');
    }

    private function generateUniqueRefNumber(): string
    {
        do {
            $ref = 'REF-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        } while (LibraryTransaction::where('ref_number', $ref)->exists());

        return $ref;
    }

    // ── Single Delete ─────────────────────────────────────────────────────────

    public function deleteConfirmed(): void
    {
        if (!$this->deletingId) return;

        $book = ArchivesLibrary::findOrFail($this->deletingId);

        LibraryTransaction::create([
            'transaction_name' => "Book Permanently Deleted - \"{$book->title}\" by {$book->author} - By Admin",
            'ref_number'       => $this->generateUniqueRefNumber(),
        ]);

        $book->delete();

        $this->deletingId  = null;
        $this->selectedIds = [];

        session()->flash('message', 'Book permanently deleted.');
    }

    // ── Bulk Delete ───────────────────────────────────────────────────────────

    public function deleteSelectedConfirmed(): void
    {
        if (empty($this->selectedIds)) return;

        $books = ArchivesLibrary::whereIn('id', $this->selectedIds)->get();
        $count = $books->count();

        $titles = $books->map(fn($b) => "\"{$b->title}\" by {$b->author}")->join(', ');

        LibraryTransaction::create([
            'transaction_name' => "Bulk Book Delete ({$count} book(s)) - {$titles} - By Admin",
            'ref_number'       => $this->generateUniqueRefNumber(),
        ]);

        $books->each->delete();

        $this->selectedIds = [];
        $this->selectAll   = false;

        session()->flash('message', "{$count} book(s) permanently deleted.");
    }

    // ── Select-all helper ─────────────────────────────────────────────────────

    public function updatedSelectAll(bool $value): void
    {
        $this->selectedIds = $value
            ? ArchivesLibrary::pluck('id')->map(fn($id) => (string) $id)->toArray()
            : [];
    }

    // ── Placeholder / Render ──────────────────────────────────────────────────

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
