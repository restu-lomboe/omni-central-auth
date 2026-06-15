<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('omni-central-auth.server.app_name') }}</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        omni: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#FF6B35',
                            600: '#ea580c',
                            700: '#c2410c',
                        }
                    }
                }
            }
        }
    </script>
    @livewireStyles
</head>

<body class="bg-gray-50 text-gray-900 antialiased">

    {{ $slot }}

    @livewireScripts
</body>

</html>
