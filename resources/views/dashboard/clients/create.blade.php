<x-omni::layouts.dashboard :title="'Tambah OAuth Client'">

    <div class="max-w-xl">
        <div class="mb-6">
            <a href="{{ route('omni.dashboard.clients.index') }}"
               class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-omni-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke OAuth Clients
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-1">Buat OAuth Client Baru</h2>
            <p class="text-sm text-gray-500 mb-6">
                Daftarkan aplikasi yang akan terhubung ke SSO server ini.
                Setelah dibuat, kamu akan mendapatkan <strong>Client ID</strong> dan <strong>Client Secret</strong>.
            </p>

            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('omni.dashboard.clients.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nama Aplikasi <span class="text-red-500">*</span>
                    </label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                           placeholder="contoh: Aplikasi HR, CRM, Keuangan..."
                           required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-omni-500 focus:border-transparent
                                  @error('name') border-red-500 @enderror">
                    <p class="text-xs text-gray-400 mt-1">Nama yang ditampilkan di halaman consent OAuth.</p>
                </div>

                <div>
                    <label for="redirect" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Redirect URI <span class="text-red-500">*</span>
                    </label>
                    <input id="redirect" type="url" name="redirect" value="{{ old('redirect') }}"
                           placeholder="https://app-anda.com/omni/callback"
                           required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono
                                  focus:outline-none focus:ring-2 focus:ring-omni-500 focus:border-transparent
                                  @error('redirect') border-red-500 @enderror">
                    <p class="text-xs text-gray-400 mt-1">
                        URL callback di aplikasi klien setelah user login.
                        Jika pakai package ini di client, biasanya: <code class="bg-gray-100 px-1 rounded">https://your-app.com/omni/callback</code>
                    </p>
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="submit"
                            class="bg-omni-500 hover:bg-omni-600 text-white font-medium px-5 py-2.5
                                   rounded-lg text-sm transition-colors">
                        Buat Client
                    </button>
                    <a href="{{ route('omni.dashboard.clients.index') }}"
                       class="px-5 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg text-sm
                              hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        {{-- Info box --}}
        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-xl p-4">
            <h3 class="text-sm font-medium text-blue-800 mb-2">💡 Cara pakai di aplikasi klien</h3>
            <p class="text-xs text-blue-700 leading-relaxed">
                Setelah client dibuat, copy <strong>Client ID</strong> dan <strong>Client Secret</strong>,
                lalu isi di <code class="bg-blue-100 px-1 rounded">.env</code> aplikasi klien:
            </p>
            <pre class="mt-2 text-xs text-blue-800 bg-blue-100 rounded p-3 overflow-auto">OMNI_AUTH_MODE=client
OMNI_CLIENT_SERVER_URL={{ config('app.url') }}
OMNI_CLIENT_ID=&lt;client-id&gt;
OMNI_CLIENT_SECRET=&lt;client-secret&gt;</pre>
        </div>
    </div>

</x-omni::layouts.dashboard>
