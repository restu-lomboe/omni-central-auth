<x-omni::layouts.dashboard :title="'Overview'">

    {{-- Stats cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm text-gray-500 mb-1">Total OAuth Clients</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_clients'] }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm text-gray-500 mb-1">Total Users</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm text-gray-500 mb-1">Login Hari Ini</p>
            <p class="text-3xl font-bold text-green-600">{{ $stats['logins_today'] }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm text-gray-500 mb-1">Gagal Login Hari Ini</p>
            <p class="text-3xl font-bold text-red-500">{{ $stats['failed_today'] }}</p>
        </div>

    </div>

    {{-- Quick links --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php $prefix = config('omni-central-auth.dashboard.prefix', 'omni-dashboard'); @endphp

        <a href="/{{ $prefix }}/clients"
           class="bg-white rounded-xl border border-gray-200 p-6 hover:border-omni-500 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 bg-omni-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-omni-500 transition-colors">
                <svg class="w-5 h-5 text-omni-500 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 mb-1">OAuth Clients</h3>
            <p class="text-sm text-gray-500">Kelola aplikasi yang terhubung ke SSO server ini.</p>
        </a>

        <a href="/{{ $prefix }}/users"
           class="bg-white rounded-xl border border-gray-200 p-6 hover:border-omni-500 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 bg-omni-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-omni-500 transition-colors">
                <svg class="w-5 h-5 text-omni-500 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 mb-1">Users & Roles</h3>
            <p class="text-sm text-gray-500">Kelola pengguna dan hak akses mereka.</p>
        </a>

        <a href="/{{ $prefix }}/audit-log"
           class="bg-white rounded-xl border border-gray-200 p-6 hover:border-omni-500 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 bg-omni-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-omni-500 transition-colors">
                <svg class="w-5 h-5 text-omni-500 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 mb-1">Audit Log</h3>
            <p class="text-sm text-gray-500">Pantau aktivitas login dan penggunaan token.</p>
        </a>
    </div>

</x-omni::layouts.dashboard>
