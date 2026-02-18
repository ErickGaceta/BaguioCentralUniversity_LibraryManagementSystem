<?php

namespace App\Livewire\Pages\Archives;

use App\Models\ArchivesLibrary;
use Livewire\Component;
use Livewire\WithPagination;

class Libraries extends Component
{
    use WithPagination;

        public $showRestoreModal = false;
            public $restoringBookId = null;

                public function openRestoreModal($bookId)
                    {
                            $this->restoringBookId = $bookId;
                                    $this->showRestoreModal = true;
                                        }

                                            public function closeRestoreModal()
                                                {
                                                        $this->showRestoreModal = false;
                                                                $this->restoringBookId = null;
                                                                    }

                                                                        public function restoreBook()
                                                                            {
                                                                                    $archivedBook = ArchivesLibrary::findOrFail($this->restoringBookId);

                                                                                            // Restore to books table
                                                                                                    \App\Models\Book::create([
                                                                                                                'title' => $archivedBook->title,
                                                                                                                            'author' => $archivedBook->author,
                                                                                                                                        'publication_date' => $archivedBook->publication_date,
                                                                                                                                                    'publisher' => $archivedBook->publisher,
                                                                                                                                                                'isbn' => $archivedBook->isbn,
                                                                                                                                                                            'department_id' => $archivedBook->department_id,
                                                                                                                                                                                        'category' => $archivedBook->category ?? 'General',
                                                                                                                                                                                                    'copies' => $archivedBook->copies,
                                                                                                                                                                                                            ]);

                                                                                                                                                                                                                    // Delete from archive
                                                                                                                                                                                                                            $archivedBook->delete();

                                                                                                                                                                                                                                    $this->closeRestoreModal();
                                                                                                                                                                                                                                            session()->flash('message', 'Book restored successfully!');
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
