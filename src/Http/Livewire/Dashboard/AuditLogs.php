<?php

namespace DeveloperAwam\OmniCentralAuth\Http\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithPagination;
use DeveloperAwam\OmniCentralAuth\Models\AuditLog;

class AuditLogs extends Component
{
    use WithPagination;

    public string $search = '';
    public string $eventFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingEventFilter(): void { $this->resetPage(); }

    public function render()
    {
        $logs = AuditLog::with('user')
            ->when($this->search, fn ($q) => $q->whereHas('user', function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->when($this->eventFilter, fn ($q) => $q->where('event', $this->eventFilter))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('occurred_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('occurred_at', '<=', $this->dateTo))
            ->orderByDesc('occurred_at')
            ->paginate(50);

        $events = AuditLog::distinct()->pluck('event');

        return view('omni::livewire.dashboard.audit-logs', compact('logs', 'events'));
    }
}
