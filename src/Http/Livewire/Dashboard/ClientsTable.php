<?php

namespace DeveloperAwam\OmniCentralAuth\Http\Livewire\Dashboard;

use Laravel\Passport\Client;
use Livewire\Component;
use Livewire\WithPagination;

class ClientsTable extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showSecret = false;

    protected $clients;

    public function boot(Client $clients): void
    {
        $this->clients = $clients;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function revokeClient($clientId)
    {
        $client = $this->clients->find($clientId);
        $client->update(['revoked' => true]);
        if ($client) {
            $this->dispatch('notify', message: 'Client revoked successfully.');
        }
    }

    public function restoreClient($clientId)
    {
        $client = $this->clients->find($clientId);
        $client->update(['revoked' => false]);
        if ($client) {
            $this->dispatch('notify', message: 'Client restored successfully.');
        }
    }

    public function render()
    {
        $allClients = $this->clients->all();

        if ($this->search) {
            $allClients = $allClients->filter(
                fn ($c) => str_contains(strtolower($c->name), strtolower($this->search))
            );
        }

        return view('omni::livewire.dashboard.clients-table', [
            'clients' => $allClients,
        ]);
    }
}
