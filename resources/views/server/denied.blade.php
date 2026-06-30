<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SSO Denied</title>
</head>
<body>
    <p style="text-align:center;padding-top:40vh;font-family:sans-serif;color:#666;font-size:14px;">
        Authorization denied — closing window...
    </p>

    <script>
        if (window.opener) {
            window.opener.postMessage({
                source:       'omni_sso',
                denied:       true,
            }, '*');
            window.close();
        }
    </script>
</body>
</html>