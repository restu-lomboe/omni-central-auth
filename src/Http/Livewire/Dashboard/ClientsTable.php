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

    public function revokeClient(int $clientId): void
    {
        $client = $this->clients->find($clientId);

        if ($client) {
            $this->clients->delete($client);
            session()->flash('success', 'Client berhasil direvoke.');
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
