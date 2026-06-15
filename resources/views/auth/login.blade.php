<x-omni::layouts.app :title="__('omni::auth.login_title')">
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-sm">

            {{-- Logo / Brand --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-omni-500 rounded-2xl mb-4">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">{{ config('omni-central-auth.server.app_name') }}</h1>
                <p class="text-gray-500 text-sm mt-1">{{ __('omni::auth.login_subtitle') }}</p>
            </div>

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('omni::auth.email') }}
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-omni-500 focus:border-transparent
                                      @error('email') border-red-500 @enderror">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('omni::auth.password') }}
                        </label>
                        <input id="password" type="password" name="password" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-omni-500 focus:border-transparent">
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-omni-500">
                            {{ __('omni::auth.remember_me') }}
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-sm text-omni-500 hover:text-omni-600">
                                {{ __('omni::auth.forgot_password') }}
                            </a>
                        @endif
                    </div>

                    <button type="submit"
                            class="w-full bg-omni-500 hover:bg-omni-600 text-white font-medium py-2.5 px-4
                                   rounded-lg text-sm transition-colors focus:outline-none focus:ring-2
                                   focus:ring-omni-500 focus:ring-offset-2">
                        {{ __('omni::auth.login_button') }}
                    </button>
                </form>

                @if (config('omni-central-auth.server.registration'))
                    <p class="text-center text-sm text-gray-500 mt-6">
                        {{ __('omni::auth.no_account') }}
                        <a href="{{ route('register') }}" class="text-omni-500 hover:text-omni-600 font-medium">
                            {{ __('omni::auth.register_link') }}
                        </a>
                    </p>
                @endif
            </div>
        </div>
    </div>
</x-omni::layouts.app>
