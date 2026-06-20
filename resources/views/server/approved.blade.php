<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SSO Authorized</title>
</head>
<body>
    <p style="text-align:center;padding-top:40vh;font-family:sans-serif;color:#666;font-size:14px;">
        Authorized — closing window...
    </p>

    <script>
        var ssoData      = @json($sso_data);
        var callbackUrl  = @json($callback_url);
        var sep          = callbackUrl.indexOf('?') === -1 ? '?' : '&';
        var redirectTo   = callbackUrl + sep + 'sso_data=' + encodeURIComponent(ssoData);

        if (window.opener) {
            window.opener.postMessage({
                source:       'omni_sso',
                redirect_url: redirectTo,
            }, '*');
            window.close();
        } else {
            window.location.href = redirectTo;
        }
    </script>
</body>
</html>