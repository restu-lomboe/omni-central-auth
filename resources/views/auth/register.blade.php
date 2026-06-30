<x-omni::layouts.app :title="__('omni::auth.register_title')">
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-sm">

            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-omni-500 rounded-2xl mb-4">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('omni::auth.register_title') }}</h1>
                <p class="text-gray-500 text-sm mt-1">{{ config('omni-central-auth.server.app_name') }}</p>
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

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('omni::auth.name') }}
                        </label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-omni-500 focus:border-transparent
                                      @error('name') border-red-500 @enderror">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('omni::auth.email') }}
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
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
                                      focus:outline-none focus:ring-2 focus:ring-omni-500 focus:border-transparent
                                      @error('password') border-red-500 @enderror">
                        <p class="text-xs text-gray-400 mt-1">{{ __('omni::auth.password_hint') }}</p>
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
                        {{ __('omni::auth.register_button') }}
                    </button>
                </form>

                <p class="text-center text-sm text-gray-500 mt-6">
                    {{ __('omni::auth.have_account') }}
                    <a href="{{ route('login') }}" class="text-omni-500 hover:text-omni-600 font-medium">
                        {{ __('omni::auth.login_link') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-omni::layouts.app>
