<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SSO Login</title>
</head>
<body>
    <script>
        (function () {
            var homeUrl = @json($home_url ?? '/');

            try {
                if (window.opener && !window.opener.closed) {
                    window.opener.location.href = homeUrl;
                    window.close();
                    return;
                }
            } catch(e) {
                // cross-origin fallback — tidak mungkin terjadi karena same-origin
            }

            window.location.href = homeUrl;
        })();
    </script>
</body>
</html>