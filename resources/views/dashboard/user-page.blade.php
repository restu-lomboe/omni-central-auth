<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Dashboard — {{ config('omni-central-auth.server.app_name') }}</title>
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
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
    <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-omni-500 font-bold text-lg">⬡ Omni Central Auth</span>
            <span class="text-gray-400 text-sm">User Dashboard</span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-500 hover:text-red-500 transition-colors">
                Logout
            </button>
        </form>
    </header>

    <main class="max-w-6xl mx-auto w-full p-8 space-y-6">
        @if (session('status'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="grid sm:grid-cols-3 gap-6">
            {{-- Kartu info user --}}
            <div class="bg-white rounded-xl border border-gray-200 p-8">
                <div class="flex flex-col items-center text-center mb-6 pb-6 border-b border-gray-100">
                    <div class="relative shrink-0 mb-4">
                        @if (auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}"
                                class="w-20 h-20 rounded-full object-cover">
                        @else
                            <div
                                class="w-20 h-20 rounded-full bg-omni-500 flex items-center justify-center text-3xl font-bold text-white">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <h1 class="text-xl font-bold text-gray-900">{{ auth()->user()->name }}</h1>
                    <p class="text-gray-500 text-sm">Member since {{ auth()->user()->created_at->format('M d, Y') }}</p>
                </div>

                <div class="space-y-4 text-sm">
                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Email</label>
                        <p class="text-gray-900">{{ auth()->user()->email }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">User
                            ID</label>
                        <p class="text-gray-900 font-mono text-xs">{{ auth()->user()->getAuthIdentifier() }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Email
                            Verified</label>
                        @if (method_exists(auth()->user(), 'hasVerifiedEmail') && auth()->user()->hasVerifiedEmail())
                            <span
                                class="text-sm font-medium text-green-700 bg-green-50 border border-green-200 px-2.5 py-0.5 rounded-full">Verified</span>
                        @else
                            <span
                                class="text-sm font-medium text-yellow-700 bg-yellow-50 border border-yellow-200 px-2.5 py-0.5 rounded-full">Not
                                Verified</span>
                        @endif
                    </div>
                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Registered</label>
                        <p class="text-gray-900">{{ auth()->user()->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Last
                            Updated</label>
                        <p class="text-gray-900">{{ auth()->user()->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>

            {{-- Kolom kanan --}}
            <div class="col-span-2 space-y-6">
                {{-- Form edit profile --}}
                <div class="bg-white rounded-xl border border-gray-200 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">Edit Profile</h2>
                        <button type="button" onclick="toggleEdit()"
                            class="text-sm font-medium text-omni-500 hover:text-omni-600 transition-colors">
                            ✏️ Edit
                        </button>
                    </div>

                    <div id="profile-info" class="space-y-5">
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Name</label>
                            <p class="text-gray-900 font-medium">{{ auth()->user()->name }}</p>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Email</label>
                            <p class="text-gray-900 font-medium">{{ auth()->user()->email }}</p>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Avatar</label>
                            <div class="flex items-center gap-3">
                                @if (auth()->user()->avatar)
                                    <img src="{{ Storage::url(auth()->user()->avatar) }}"
                                        class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div
                                        class="w-10 h-10 rounded-full bg-omni-100 flex items-center justify-center text-sm font-bold text-omni-500">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                @endif
                                <span
                                    class="text-sm text-gray-400">{{ auth()->user()->avatar ? basename(auth()->user()->avatar) : 'No avatar' }}</span>
                            </div>
                        </div>
                    </div>

                    <form id="profile-form" method="POST" action="{{ route('user.profile.update') }}"
                        enctype="multipart/form-data" class="space-y-5 hidden">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input id="name" name="name" type="text"
                                value="{{ old('name', auth()->user()->name) }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-omni-500 focus:border-omni-500 outline-none transition-all">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input id="email" name="email" type="email"
                                value="{{ old('email', auth()->user()->email) }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-omni-500 focus:border-omni-500 outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Avatar</label>
                            <div class="flex items-center gap-4">
                                <div class="relative shrink-0">
                                    @if (auth()->user()->avatar)
                                        <img id="form-avatar-preview" src="{{ Storage::url(auth()->user()->avatar) }}"
                                            class="w-16 h-16 rounded-full object-cover border-2 border-gray-200">
                                    @else
                                        <div id="form-avatar-preview"
                                            class="w-16 h-16 rounded-full bg-omni-100 flex items-center justify-center text-2xl font-bold text-omni-500 border-2 border-gray-200">
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="avatar" id="avatar-input"
                                        accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-omni-50 file:text-omni-600 hover:file:bg-omni-100">
                                    <p class="text-xs text-gray-400 mt-1">Max 2MB. Format: JPEG, PNG, JPG, GIF, WebP</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit"
                                class="bg-omni-500 text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-omni-600 transition-colors">
                                Save Changes
                            </button>
                            <button type="button" onclick="toggleEdit()"
                                class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Form ganti password --}}
                <div class="bg-white rounded-xl border border-gray-200 p-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Change Password</h2>

                    <form method="POST" action="{{ route('user.password.update') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current
                                Password</label>
                            <input id="current_password" name="current_password" type="password"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-omni-500 focus:border-omni-500 outline-none transition-all">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New
                                Password</label>
                            <input id="password" name="password" type="password"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-omni-500 focus:border-omni-500 outline-none transition-all">
                        </div>

                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-omni-500 focus:border-omni-500 outline-none transition-all">
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="bg-omni-500 text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-omni-600 transition-colors">
                                Save New Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleEdit() {
            document.getElementById('profile-info').classList.toggle('hidden');
            document.getElementById('profile-form').classList.toggle('hidden');
        }

        document.getElementById('avatar-input')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(ev) {
                const preview = document.getElementById('form-avatar-preview');
                if (preview.tagName === 'IMG') {
                    preview.src = ev.target.result;
                } else {
                    const img = document.createElement('img');
                    img.id = 'form-avatar-preview';
                    img.className = 'w-16 h-16 rounded-full object-cover border-2 border-gray-200';
                    img.src = ev.target.result;
                    preview.parentNode.replaceChild(img, preview);
                }
            };
            reader.readAsDataURL(file);
        });
    </script>
</body>

</html>
