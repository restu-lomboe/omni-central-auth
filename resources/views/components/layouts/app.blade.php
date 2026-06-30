<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('omni-central-auth.server.app_name') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    @endif
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

<body class="bg-gray-50 text-gray-900 antialiased">

    {{ $slot }}

    @livewireScripts
</body>

</html>
