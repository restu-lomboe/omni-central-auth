<?php

namespace DeveloperAwam\OmniCentralAuth\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Laravel\Passport\Client;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::where('revoked', false)->get();

        return view('omni::dashboard.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('omni::dashboard.clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'redirect'     => ['required', 'url'],
        ]);

        Client::create([
            'user_id'                => auth()->id(),
            'name'                   => $request->name,
            'secret'                 => Str::random(40),
            'redirect'               => $request->redirect,
            'personal_access_client' => false,
            'password_client'        => false,
            'revoked'                => false,
        ]);

        return redirect()->route('omni.dashboard.clients.index')
            ->with('success', "Client \"{$request->name}\" created successfully.");
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);

        abort_if(! $client, 404);

        return view('omni::dashboard.clients.edit', compact('client'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'redirect' => ['required', 'url'],
        ]);

        $client = Client::findOrFail($id);
        abort_if(! $client, 404);

        $client->update(['name' => $request->name, 'redirect' => $request->redirect]);

        return redirect()->route('omni.dashboard.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        abort_if(! $client, 404);

        $client->update(['revoked' => true]);

        return redirect()->route('omni.dashboard.clients.index')
            ->with('success', 'Client berhasil dihapus.');
    }
}
