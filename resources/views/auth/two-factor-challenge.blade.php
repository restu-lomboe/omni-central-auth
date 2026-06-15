<x-omni::layouts.app :title="__('omni::auth.2fa_title')">
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-sm">

            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-omni-500 rounded-2xl mb-4">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('omni::auth.2fa_title') }}</h1>
                <p class="text-gray-500 text-sm mt-1 max-w-xs mx-auto" x-data="{ useRecovery: false }" x-show="!useRecovery">
                    {{ __('omni::auth.2fa_desc') }}
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8" x-data="{ useRecovery: false }">

                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- OTP Code Form --}}
                <form x-show="!useRecovery" method="POST" action="{{ route('two-factor.login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('omni::auth.2fa_code') }}
                        </label>
                        <input id="code" type="text" name="code"
                               inputmode="numeric" autocomplete="one-time-code" maxlength="6"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-center
                                      tracking-[0.5em] font-mono text-lg
                                      focus:outline-none focus:ring-2 focus:ring-omni-500 focus:border-transparent"
                               autofocus>
                    </div>

                    <button type="submit"
                            class="w-full bg-omni-500 hover:bg-omni-600 text-white font-medium py-2.5 px-4
                                   rounded-lg text-sm transition-colors">
                        {{ __('omni::auth.2fa_verify') }}
                    </button>
                </form>

                {{-- Recovery Code Form --}}
                <form x-show="useRecovery" method="POST" action="{{ route('two-factor.login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="recovery_code" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('omni::auth.2fa_recovery_code') }}
                        </label>
                        <input id="recovery_code" type="text" name="recovery_code"
                               autocomplete="one-time-code"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono
                                      focus:outline-none focus:ring-2 focus:ring-omni-500 focus:border-transparent">
                        <p class="text-xs text-gray-400 mt-1">{{ __('omni::auth.2fa_recovery_hint') }}</p>
                    </div>

                    <button type="submit"
                            class="w-full bg-omni-500 hover:bg-omni-600 text-white font-medium py-2.5 px-4
                                   rounded-lg text-sm transition-colors">
                        {{ __('omni::auth.2fa_verify') }}
                    </button>
                </form>

                <div class="text-center mt-5">
                    <button type="button"
                            @click="useRecovery = !useRecovery"
                            class="text-sm text-omni-500 hover:text-omni-600">
                        <span x-show="!useRecovery">{{ __('omni::auth.2fa_use_recovery') }}</span>
                        <span x-show="useRecovery">{{ __('omni::auth.2fa_use_code') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</x-omni::layouts.app>
