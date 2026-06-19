<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>SSO Login</title>
</head>

<body>
    <p style="text-align:center;padding-top:40vh;font-family:sans-serif;color:#666;font-size:14px;">
        @if ($success)
            Login successful — closing window...
        @else
            Login failed — closing window...
        @endif
    </p>

    <script>
        (function() {
            var success = @json($success);
            var homeUrl = @json($home_url ?? '/');

            if (window.opener) {
                window.opener.postMessage({
                    source: 'omni_sso',
                    success: success,
                }, '*');
                window.close();
            } else {
                window.location.href = homeUrl;
            }
        })();
    </script>
</body>

</html>
