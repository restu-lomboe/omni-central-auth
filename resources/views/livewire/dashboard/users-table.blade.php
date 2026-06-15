<div>
    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <input wire:model.live="search" type="text" placeholder="Cari nama atau email..."
               class="w-64 px-4 py-2 border border-gray-300 rounded-lg text-sm
                      focus:outline-none focus:ring-2 focus:ring-omni-500">

        <select wire:model.live="roleFilter"
                class="px-4 py-2 border border-gray-300 rounded-lg text-sm
                       focus:outline-none focus:ring-2 focus:ring-omni-500">
            <option value="">Semua Role</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
        </select>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">User</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Role</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Terdaftar</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">2FA</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-omni-500 flex items-center justify-center
                                            text-white text-xs font-bold flex-shrink-0">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-gray-400 text-xs">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if ($user->id !== auth()->id())
                                <select wire:change="updateRole({{ $user->id }}, $event.target.value)"
                                        class="px-2 py-1 border border-gray-200 rounded text-xs
                                               focus:outline-none focus:ring-2 focus:ring-omni-500
                                               {{ ($user->role ?? 'user') === 'admin' ? 'text-omni-600 font-medium' : 'text-gray-600' }}">
                                    <option value="user" @selected(($user->role ?? 'user') === 'user')>User</option>
                                    <option value="admin" @selected(($user->role ?? 'user') === 'admin')>Admin</option>
                                </select>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-omni-100 text-omni-700">
                                    {{ $user->role ?? 'admin' }} (kamu)
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-xs">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            @if (! empty($user->two_factor_secret))
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if ($user->id !== auth()->id())
                                <button wire:click="deleteUser({{ $user->id }})"
                                        wire:confirm="Yakin hapus {{ $user->name }}? Data tidak bisa dikembalikan."
                                        class="text-red-400 hover:text-red-600 text-xs">
                                    Hapus
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                            Tidak ada user ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
