<div>
    <div class="flex items-center justify-between mb-6">
        <input wire:model.live="search" type="text" placeholder="Search clients..."
            class="w-64 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-omni-500">
        <a href="{{ route('omni.dashboard.clients.create') }}"
            class="bg-omni-500 hover:bg-omni-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            + Add Client
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Name</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Client ID</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Redirect URI</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Status</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($clients as $client)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $client->name }}</td>
                        <td class="px-6 py-4 font-mono text-gray-500 text-xs">{{ $client->id }}</td>
                        <td class="px-6 py-4 text-gray-500 max-w-xs truncate">
                            @php
                                $raw = $client->getAttributes()['redirect_uris'] ?? null;
                                $redirects = [];

                                if ($raw !== null) {
                                    $decoded = json_decode($raw, true);

                                    if (is_array($decoded)) {
                                        $redirects = $decoded;
                                    } elseif ($decoded !== null && $decoded !== false) {
                                        // decoded JSON is a scalar (string/number) — wrap it
                                        $redirects = [$decoded];
                                    } else {
                                        // Fallback: if raw is a plain string (not JSON), use it
                                        $redirects = is_string($raw) && $raw !== '' ? [$raw] : [];
                                    }
                                }
                            @endphp
                            {{ $redirects[0] ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($client->revoked)
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">Revoked</span>
                            @else
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Active</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-3">
                            <a href="{{ route('omni.dashboard.clients.edit', $client->id) }}"
                                class="text-omni-500 hover:text-omni-600 text-xs font-medium">Edit</a>
                            @if ($client->revoked)
                                <button type="button" wire:click="restoreClient('{{ $client->id }}')"
                                    wire:confirm="Are you sure you want to restore this client?"
                                    class="text-omni-500 hover:text-omni-600 text-xs font-medium">
                                    Restore
                                </button>
                            @else
                                <button type="button" wire:click="revokeClient('{{ $client->id }}')"
                                    wire:confirm="Are you sure you want to revoke this client?"
                                    class="text-red-500 hover:text-red-600 text-xs font-medium">
                                    Revoke
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                            No OAuth clients yet. <a href="{{ route('omni.dashboard.clients.create') }}"
                                class="text-omni-500">Create now →</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
