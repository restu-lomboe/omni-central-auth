<x-omni::layouts.dashboard :title="'Edit OAuth Client'">

    <div class="max-w-xl">
        <div class="mb-6">
            <a href="{{ route('omni.dashboard.clients.index') }}"
                class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-omni-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke OAuth Clients
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-6">Edit Client: {{ $client->name }}</h2>

            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Read-only info --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 mb-6 space-y-3">
                <div>
                    <p class="text-xs font-medium text-gray-500 mb-0.5">Client ID</p>
                    <p class="text-sm font-mono text-gray-800 select-all">{{ $client->id }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 mb-0.5">Client Secret</p>
                    <p class="text-sm font-mono text-gray-800 select-all blur-sm hover:blur-none transition-all cursor-pointer"
                        title="Klik untuk melihat">
                        {{ $client->secret }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">Hover untuk melihat secret.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('omni.dashboard.clients.update', $client->id) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nama Aplikasi <span class="text-red-500">*</span>
                    </label>
                    <input id="name" type="text" name="name" value="{{ old('name', $client->name) }}"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-omni-500 focus:border-transparent
                                  @error('name') border-red-500 @enderror">
                </div>

                <div>
                    <label for="redirect" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Redirect URI <span class="text-red-500">*</span>
                    </label>
                    <input id="redirect" type="url" name="redirect"
                        value="{{ old('redirect', json_decode($client->getAttributes()['redirect_uris'] ?? '[]', true)[0] ?? '') }}"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono
                                  focus:outline-none focus:ring-2 focus:ring-omni-500 focus:border-transparent
                                  @error('redirect') border-red-500 @enderror">
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="submit"
                        class="bg-omni-500 hover:bg-omni-600 text-white font-medium px-5 py-2.5
                                   rounded-lg text-sm transition-colors">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('omni.dashboard.clients.index') }}"
                        class="px-5 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg text-sm
                              hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        {{-- Danger zone --}}
        <div class="mt-4 bg-red-50 border border-red-200 rounded-xl p-5">
            <h3 class="text-sm font-semibold text-red-800 mb-1">Danger Zone</h3>
            <p class="text-xs text-red-600 mb-4">
                Menghapus client akan mencabut semua token yang diterbitkan untuk aplikasi ini.
                Pengguna yang sedang login via client ini akan ter-logout.
            </p>
            <form method="POST" action="{{ route('omni.dashboard.clients.destroy', $client->id) }}"
                onsubmit="return confirm('Yakin ingin menghapus client {{ $client->name }}? Semua token akan dicabut.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium
                               rounded-lg transition-colors">
                    Hapus Client
                </button>
            </form>
        </div>
    </div>

</x-omni::layouts.dashboard>
