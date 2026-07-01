<?php

namespace DeveloperAwam\OmniCentralAuth\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function index()
    {
        $userModel = config('omni-central-auth.user_model');
        $users = $userModel::latest()->paginate(20);

        return view('omni::dashboard.users.index', compact('users'));
    }

    public function updateRole(Request $request, $userId)
    {
        $request->validate([
            'role' => ['required', 'string', 'in:user,admin'],
        ]);

        $userModel = config('omni-central-auth.user_model');
        $user = $userModel::findOrFail($userId);

        // Cegah admin mengubah role dirinya sendiri
        if ($user->id === auth()->id()) {
            return back()->withErrors(['role' => 'Cannot change your own role.']);
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', "{$user->name}'s role updated to {$request->role}.");
    }

    public function destroy($userId)
    {
        $userModel = config('omni-central-auth.user_model');
        $user = $userModel::findOrFail($userId);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['delete' => 'Cannot delete your own account.']);
        }

        $user->delete();

        return back()->with('success', "{$user->name} deleted successfully.");
    }
}
