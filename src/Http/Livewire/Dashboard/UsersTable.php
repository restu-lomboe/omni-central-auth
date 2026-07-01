<?php

namespace DeveloperAwam\OmniCentralAuth\Http\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithPagination;

class UsersTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $roleFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updateRole(int $userId, string $role): void
    {
        $userModel = config('omni-central-auth.user_model');
        $user = $userModel::findOrFail($userId);

        if ($user->id === auth()->id()) {
            $this->addError('role', 'Cannot change your own role.');

            return;
        }

        $user->update(['role' => $role]);

        $this->dispatch('role-updated', userName: $user->name, role: $role);
    }

    public function deleteUser(int $userId): void
    {
        $userModel = config('omni-central-auth.user_model');
        $user = $userModel::findOrFail($userId);

        if ($user->id === auth()->id()) {
            $this->addError('delete', 'Cannot delete your own account.');

            return;
        }

        $user->delete();

        session()->flash('success', "{$user->name} deleted successfully.");
    }

    public function render()
    {
        $userModel = config('omni-central-auth.user_model');

        $users = $userModel::query()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->when($this->roleFilter, fn ($q) => $q->where('role', $this->roleFilter))
            ->latest()
            ->paginate(20);

        return view('omni::livewire.dashboard.users-table', compact('users'));
    }
}
