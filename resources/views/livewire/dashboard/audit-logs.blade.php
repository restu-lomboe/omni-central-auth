<div>
    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <input wire:model.live="search" type="text" placeholder="Cari user..."
            class="w-52 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-omni-500">

        <select wire:model.live="eventFilter"
            class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-omni-500">
            <option value="">Semua Event</option>
            @foreach ($events as $event)
                <option value="{{ $event }}">{{ $event }}</option>
            @endforeach
        </select>

        <input wire:model.live="dateFrom" type="date"
            class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-omni-500">
        <span class="text-gray-400 text-sm">s/d</span>
        <input wire:model.live="dateTo" type="date"
            class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-omni-500">
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">User</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Event</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">IP Address</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Client App</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            @if ($log->user)
                                <p class="font-medium text-gray-900">{{ $log->user->name }}</p>
                                <p class="text-gray-400 text-xs">{{ $log->user->email }}</p>
                            @else
                                <span class="text-gray-400 text-xs italic">Guest</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            @php
                                $badge = match ($log->event) {
                                    'login' => 'bg-green-100 text-green-700',
                                    'logout' => 'bg-gray-100 text-gray-600',
                                    'login_failed' => 'bg-red-100 text-red-700',
                                    'token_issued' => 'bg-blue-100 text-blue-700',
                                    'token_revoked' => 'bg-yellow-100 text-yellow-700',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $badge }}">
                                {{ $log->event }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-500 font-mono text-xs">{{ $log->ip_address }}</td>
                        <td class="px-6 py-3 text-gray-500 text-xs">{{ $log->client_app ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-400 text-xs">{{ $log->occurred_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                            Belum ada aktivitas yang tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
