<x-omni::layouts.app :title="__('omni::auth.authorize_title')">
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-md">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- Header --}}
                <div class="bg-gray-900 px-8 py-6 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-omni-500 rounded-xl mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h1 class="text-white font-bold text-lg">{{ config('omni-central-auth.server.app_name') }}</h1>
                    <p class="text-gray-400 text-xs mt-0.5">Identity Provider</p>
                </div>

                <div class="px-8 py-6">

                    {{-- App requesting access --}}
                    <div class="text-center mb-6">
                        <div
                            class="inline-flex items-center justify-center w-14 h-14 bg-gray-100 rounded-2xl border border-gray-200 mb-3">
                            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $client->name }}</h2>
                        <p class="text-gray-500 text-sm mt-1">
                            {{ __('omni::auth.authorize_request', ['app' => $client->name]) }}
                        </p>
                    </div>

                    {{-- User info --}}
                    <div class="bg-gray-50 rounded-xl border border-gray-200 px-4 py-3 mb-6 flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-full bg-omni-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                        </div>
                    </div>

                    {{-- Permissions --}}
                    <div class="mb-6">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">
                            {{ __('omni::auth.authorize_permissions') }}
                        </p>
                        <ul class="space-y-2">
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('omni::auth.perm_identity') }}
                            </li>
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('omni::auth.perm_email') }}
                            </li>
                        </ul>
                    </div>

                    {{-- Action buttons --}}
                    <div class="flex gap-3">
                        <form method="POST" action="{{ route('omni.authorize.deny') }}" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full px-4 py-2.5 border border-gray-300 text-gray-700 font-medium
                                           rounded-lg text-sm hover:bg-gray-50 transition-colors">
                                {{ __('omni::auth.authorize_deny') }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('omni.authorize.approve') }}" class="flex-1">
                            @csrf
                            <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                            <button type="submit"
                                class="w-full px-4 py-2.5 bg-omni-500 hover:bg-omni-600 text-white font-medium
                                           rounded-lg text-sm transition-colors">
                                {{ __('omni::auth.authorize_approve') }}
                            </button>
                        </form>
                    </div>

                    <p class="text-center text-xs text-gray-400 mt-4">
                        {{ __('omni::auth.authorize_footer', ['server' => config('omni-central-auth.server.app_name')]) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-omni::layouts.app>
