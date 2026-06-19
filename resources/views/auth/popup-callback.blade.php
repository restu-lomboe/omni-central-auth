<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ __('omni::auth.login_title') }}</title>
</head>

<body>
    <p style="text-align:center;padding-top:40vh;font-family:sans-serif;color:#666;">
        @if ($success)
            Login successful — closing window...
        @else
            Login failed — closing window...
        @endif
    </p>

    <script>
        (function() {
            if (!window.opener) {
                window.location.href = '{{ route('omni.login') }}';
                return;
            }

            window.opener.postMessage({
                source: 'omni_sso',
                success: @json($success),
            }, '*');

            window.close();
        })();
    </script>
</body>

</html>
