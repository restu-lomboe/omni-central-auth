<?php

namespace DeveloperAwam\OmniCentralAuth\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;

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

        $client = app(ClientRepository::class)->createAuthorizationCodeGrantClient(
            user: auth()->user(),
            name: $request->name,
            redirectUris: [$request->redirect],
            confidential: true,
            enableDeviceFlow: false,
        );

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

        $client->update([
            'name'          => $request->name,
            'redirect_uris' => [$request->redirect],
        ]);

        return redirect()->route('omni.dashboard.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        abort_if(! $client, 404);

        $client->update(['revoked' => true]);

        return redirect()->route('omni.dashboard.clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}
