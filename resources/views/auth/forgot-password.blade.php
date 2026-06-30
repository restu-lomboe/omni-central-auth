<x-omni::layouts.app :title="__('omni::auth.forgot_password_title')">
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-sm">

            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-omni-500 rounded-2xl mb-4">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('omni::auth.forgot_password_title') }}</h1>
                <p class="text-gray-500 text-sm mt-1 max-w-xs mx-auto">
                    {{ __('omni::auth.forgot_password_desc') }}
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

                @if (session('status'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
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

                    <button type="submit"
                            class="w-full bg-omni-500 hover:bg-omni-600 text-white font-medium py-2.5 px-4
                                   rounded-lg text-sm transition-colors focus:outline-none focus:ring-2
                                   focus:ring-omni-500 focus:ring-offset-2">
                        {{ __('omni::auth.send_reset_link') }}
                    </button>
                </form>

                <div class="text-center mt-6">
                    <a href="{{ route('login') }}"
                       class="text-sm text-gray-500 hover:text-omni-500 inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        {{ __('omni::auth.back_to_login') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-omni::layouts.app>
