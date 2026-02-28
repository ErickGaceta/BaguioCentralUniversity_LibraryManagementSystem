<?php

namespace App\Livewire\Pages\Reports;

use App\Models\Report;
use Livewire\Component;
use Livewire\WithPagination;

use Livewire\Attributes\Lazy;

#[Lazy] class Index extends Component
{
    use WithPagination;

    public string $search     = '';
    public string $typeFilter = '';

    protected $queryString = [
        'search'     => ['except' => ''],
        'typeFilter' => ['except' => ''],
    ];

    // ── Actions ───────────────────────────────────────────────────────────────

    public function delete(int $id): void
    {
        Report::findOrFail($id)->delete();
        session()->flash('success', 'Report deleted.');
    }

    public bool $showGenerateModal = false;

    protected $listeners = ['report-generated' => 'onReportGenerated'];

    public function onReportGenerated(string $message): void
    {
        session()->flash('success', $message);
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function placeholder()
    {
        return <<<'HTML'
            <div class="w-full h-full flex justify-center items-center align-center">
                <span class="loader"></span>
            </div>
        HTML;
    }

    public ?int $deletingId = null;

    public function deleteConfirmed(): void
    {
        $this->delete($this->deletingId);
        $this->deletingId = null;
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $reports = Report::query()
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->typeFilter, fn($q) => $q->where('report_type', $this->typeFilter))
            ->latest()
            ->paginate(12);

        return view('livewire.pages.reports.index', [
            'reports'     => $reports,
            'reportTypes' => Report::TYPES,
        ]);
    }
}
