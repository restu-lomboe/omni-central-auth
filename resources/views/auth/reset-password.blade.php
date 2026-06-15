<x-omni::layouts.app :title="__('omni::auth.reset_password_title')">
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-sm">

            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-omni-500 rounded-2xl mb-4">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('omni::auth.reset_password_title') }}</h1>
                <p class="text-gray-500 text-sm mt-1">{{ __('omni::auth.reset_password_desc') }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ request()->route('token') }}">

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('omni::auth.email') }}
                        </label>
                        <input id="email" type="email" name="email"
                               value="{{ old('email', request()->email) }}" required autofocus
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-omni-500 focus:border-transparent
                                      @error('email') border-red-500 @enderror">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('omni::auth.new_password') }}
                        </label>
                        <input id="password" type="password" name="password" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-omni-500 focus:border-transparent
                                      @error('password') border-red-500 @enderror">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('omni::auth.password_confirm') }}
                        </label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-omni-500 focus:border-transparent">
                    </div>

                    <button type="submit"
                            class="w-full bg-omni-500 hover:bg-omni-600 text-white font-medium py-2.5 px-4
                                   rounded-lg text-sm transition-colors focus:outline-none focus:ring-2
                                   focus:ring-omni-500 focus:ring-offset-2">
                        {{ __('omni::auth.reset_password_button') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-omni::layouts.app>
