<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Dashboard' }} — {{ config('omni-central-auth.server.app_name') }}</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
            --color-omni-50: #fff7ed;
            --color-omni-100: #ffedd5;
            --color-omni-500: #FF6B35;
            --color-omni-600: #ea580c;
            --color-omni-700: #c2410c;
        }
    </style>
    @livewireStyles
</head>

<body class="bg-gray-100">

    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar --}}
        <aside class="w-64 bg-gray-900 text-white flex flex-col">
            <div class="px-6 py-5 border-b border-gray-700">
                <span class="text-omni-500 font-bold text-lg">⬡ Omni Central Auth</span>
                <p class="text-gray-400 text-xs mt-0.5">Admin Dashboard</p>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1">
                @php $prefix = config('omni-central-auth.dashboard.prefix', 'omni-dashboard'); @endphp

                <a href="/{{ $prefix }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                      {{ request()->is($prefix) ? 'bg-omni-500 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                    Overview
                </a>

                @if (config('omni-central-auth.dashboard.features.clients'))
                    <a href="/{{ $prefix }}/clients"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                      {{ request()->is($prefix . '/clients*') ? 'bg-omni-500 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18" />
                        </svg>
                        OAuth Clients
                    </a>
                @endif

                @if (config('omni-central-auth.dashboard.features.users'))
                    <a href="/{{ $prefix }}/users"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                      {{ request()->is($prefix . '/users*') ? 'bg-omni-500 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0" />
                        </svg>
                        Users & Roles
                    </a>
                @endif

                @if (config('omni-central-auth.dashboard.features.audit_log'))
                    <a href="/{{ $prefix }}/audit-log"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                      {{ request()->is($prefix . '/audit-log*') ? 'bg-omni-500 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Audit Log
                    </a>
                @endif
            </nav>

            <div class="px-4 py-4 border-t border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-omni-500 flex items-center justify-center text-sm font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main content --}}
        <main class="flex-1 overflow-auto">
            {{-- Top bar --}}
            <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-800">{{ $title ?? 'Dashboard' }}</h1>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-red-500 transition-colors">
                        Logout
                    </button>
                </form>
            </header>

            <div class="p-8">
                {{-- Flash messages --}}
                @if (session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
</body>

</html>
